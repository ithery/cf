<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_CallQueuedHandler {
    /**
     * The bus dispatcher implementation.
     *
     * @var \CQueue_DispatcherInterface
     */
    protected $dispatcher;

    /**
     * The container instance.
     *
     * @var CContainer_Container
     */
    protected $container;

    /**
     * Create a new handler instance.
     *
     * @return void
     */
    public function __construct() {
        $container = CContainer::getInstance();
        $dispatcher = CQueue::dispatcher();
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the queued job.
     *
     * @param \CQueue_AbstractJob $job
     * @param array               $data
     *
     * @return void
     */
    public function call(CQueue_AbstractJob $job, array $data) {
        try {
            $command = $this->setJobInstanceIfNecessary(
                $job,
                $this->getCommand($data)
            );
        } catch (CModel_Exception_ModelNotFoundException $e) {
            return $this->handleModelNotFound($job, $e);
        }

        $this->dispatchThroughMiddleware($job, $command);

        if ($command instanceof CQueue_Contract_ShouldBeUniqueUntilProcessingInterface) {
            $this->ensureUniqueJobLockIsReleased($command);
        }

        if (!$job->isReleased() && !$command instanceof CQueue_Contract_ShouldBeUniqueUntilProcessingInterface) {
            $this->ensureUniqueJobLockIsReleased($command);
        }
        if (!$job->hasFailed() && !$job->isReleased()) {
            $this->ensureNextJobInChainIsDispatched($command);
            $this->ensureSuccessfulBatchJobIsRecorded($command);
        }
        if (!$job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    /**
     * Get the command from the given payload.
     *
     * @param array $data
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    protected function getCommand(array $data) {
        if (cstr::startsWith($data['command'], 'O:')) {
            return unserialize($data['command']);
        }

        return unserialize(CCrypt::encrypter()->decrypt($data['command']));

        //throw new RuntimeException('Unable to extract job payload.');
    }

    /**
     * Dispatch the given job / command through its specified middleware.
     *
     * @param CQueue_AbstractJob $job
     * @param mixed              $command
     *
     * @return mixed
     */
    protected function dispatchThroughMiddleware(CQueue_AbstractJob $job, $command) {
        if ($command instanceof \__PHP_Incomplete_Class) {
            throw new Exception('Job is incomplete class: ' . json_encode($command));
        }

        return (new CQueue_Pipeline($this->container))->send($command)
            ->through(array_merge(method_exists($command, 'middleware') ? $command->middleware() : [], isset($command->middleware) ? $command->middleware : []))
            ->then(function ($command) use ($job) {
                if ($command instanceof CQueue_Contract_ShouldBeUniqueUntilProcessingInterface) {
                    $this->ensureUniqueJobLockIsReleased($command);
                }

                return $this->dispatcher->dispatchNow(
                    $command,
                    $this->resolveHandler($job, $command)
                );
            });
    }

    /**
     * Resolve the handler for the given command.
     *
     * @param CQueue_AbstractJob $job
     * @param mixed              $command
     *
     * @return mixed
     */
    protected function resolveHandler($job, $command) {
        $handler = $this->dispatcher->getCommandHandler($command) ?: null;
        if ($handler) {
            $this->setJobInstanceIfNecessary($job, $handler);
        }

        return $handler;
    }

    /**
     * Set the job instance of the given class if necessary.
     *
     * @param CQueue_AbstractJob $job
     * @param mixed              $instance
     *
     * @return mixed
     */
    protected function setJobInstanceIfNecessary(CQueue_AbstractJob $job, $instance) {
        if (in_array(CQueue_Trait_InteractsWithQueue::class, c::classUsesRecursive($instance))) {
            $instance->setJob($job);
        }

        return $instance;
    }

    /**
     * Ensure the next job in the chain is dispatched if applicable.
     *
     * @param mixed $command
     *
     * @return void
     */
    protected function ensureNextJobInChainIsDispatched($command) {
        if (method_exists($command, 'dispatchNextJobInChain')) {
            $command->dispatchNextJobInChain();
        }
    }

    /**
     * Ensure the batch is notified of the successful job completion.
     *
     * @param mixed $command
     *
     * @return void
     */
    protected function ensureSuccessfulBatchJobIsRecorded($command) {
        $uses = c::classUsesRecursive($command);

        if (!in_array(CQueue_Trait_BatchableTrait::class, $uses)
            || !in_array(CQueue_Trait_InteractsWithQueue::class, $uses)
        ) {
            return;
        }

        if ($batch = $command->batch()) {
            $batch->recordSuccessfulJob($command->job->uuid());
        }
    }

    /**
     * Ensure the lock for a unique job is released.
     *
     * @param mixed $command
     *
     * @return void
     */
    protected function ensureUniqueJobLockIsReleased($command) {
        if ($command instanceof CQueue_Contract_ShouldBeUniqueInterface) {
            $lock = new CQueue_UniqueLock(c::cache()->driver());
            $lock->release($command);
        }
    }

    /**
     * Handle a model not found exception.
     *
     * @param CQueue_AbstractJob $job
     * @param \Exception         $e
     *
     * @return void
     */
    protected function handleModelNotFound(CQueue_AbstractJob $job, $e) {
        $class = $job->resolveName();

        try {
            $reflectionClass = new ReflectionClass($class);
            $reflectionProperties = $reflectionClass->getDefaultProperties();
            $shouldDelete = isset($reflectionProperties['deleteWhenMissingModels']) ? $reflectionProperties['deleteWhenMissingModels'] : false;
        } catch (Exception $e) {
            $shouldDelete = false;
        }
        if ($shouldDelete) {
            return $job->delete();
        }

        return $job->fail($e);
    }

    /**
     * Call the failed method on the job instance.
     *
     * The exception that caused the failure will be passed.
     *
     * @param array      $data
     * @param \Exception $e
     * @param string     $uuid
     *
     * @return void
     */
    public function failed(array $data, $e, $uuid) {
        $command = $this->getCommand($data);

        if (!$command instanceof CQueue_Contract_ShouldBeUniqueUntilProcessingInterface) {
            $this->ensureUniqueJobLockIsReleased($command);
        }

        if ($command instanceof \__PHP_Incomplete_Class) {
            return;
        }

        $this->ensureFailedBatchJobIsRecorded($uuid, $command, $e);
        $this->ensureChainCatchCallbacksAreInvoked($uuid, $command, $e);
        if (method_exists($command, 'failed')) {
            $command->failed($e);
        }
    }

    /**
     * Ensure the batch is notified of the failed job.
     *
     * @param string     $uuid
     * @param mixed      $command
     * @param \Throwable $e
     *
     * @return void
     */
    protected function ensureFailedBatchJobIsRecorded($uuid, $command, $e) {
        if (!in_array(CQueue_Trait_BatchableTrait::class, c::classUsesRecursive($command))) {
            return;
        }

        if ($batch = $command->batch()) {
            $batch->recordFailedJob($uuid, $e);
        }
    }

    /**
     * Ensure the chained job catch callbacks are invoked.
     *
     * @param string     $uuid
     * @param mixed      $command
     * @param \Throwable $e
     *
     * @return void
     */
    protected function ensureChainCatchCallbacksAreInvoked($uuid, $command, $e) {
        if (method_exists($command, 'invokeChainCatchCallbacks')) {
            $command->invokeChainCatchCallbacks($e);
        }
    }
}
