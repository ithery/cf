<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 4, 2019, 4:59:02 PM
 */
class CQueue_CallQueuedHandler {
    /**
     * The bus dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
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
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param array                           $data
     *
     * @return void
     */
    public function call(CQueue_AbstractJob $job, array $data) {
        try {
            $command = $this->setJobInstanceIfNecessary(
                $job,
                unserialize($data['command'])
            );
        } catch (CModel_Exception_ModelNotFound $e) {
            return $this->handleModelNotFound($job, $e);
        }

        $this->dispatchThroughMiddleware($job, $command);

        if (!$job->hasFailed() && !$job->isReleased()) {
            $this->ensureNextJobInChainIsDispatched($command);
        }
        if (!$job->isDeletedOrReleased()) {
            $job->delete();
        }
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
        return (new CQueue_Pipeline($this->container))->send($command)
                        ->through(array_merge(method_exists($command, 'middleware') ? $command->middleware() : [], isset($command->middleware) ? $command->middleware : []))
                        ->then(function ($command) use ($job) {
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
     *
     * @return void
     */
    public function failed(array $data, $e) {
        $command = unserialize($data['command']);
        if (method_exists($command, 'failed')) {
            $command->failed($e);
        }
    }
}
