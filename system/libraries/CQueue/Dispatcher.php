<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Dispatcher implements CQueue_QueueingDispatcherInterface {
    /**
     * The container implementation.
     *
     * @var CContainer_ContainerInterface
     */
    protected $container;

    /**
     * The pipeline instance for the bus.
     *
     * @var CQueue_Pipeline
     */
    protected $pipeline;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The command to handler mapping for non-self-handling events.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * The queue resolver callback.
     *
     * @var null|\Closure
     */
    protected $queueResolver;

    /**
     * Indicates if dispatching after response is disabled.
     *
     * @var bool
     */
    protected $allowsDispatchingAfterResponses = true;

    /**
     * Create a new command dispatcher instance.
     *
     * @param CContainer_ContainerInterface $container
     * @param null|\Closure                 $queueResolver
     *
     * @return void
     */
    public function __construct(CContainer_ContainerInterface $container, Closure $queueResolver = null) {
        $this->container = $container;
        $this->queueResolver = $queueResolver;
        $this->pipeline = new CQueue_Pipeline($container);
    }

    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param mixed $command
     *
     * @return mixed
     */
    public function dispatch($command) {
        if ($this->queueResolver && $this->commandShouldBeQueued($command)) {
            return $this->dispatchToQueue($command);
        }

        return $this->dispatchNow($command);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param mixed $command
     * @param mixed $handler
     *
     * @return mixed
     */
    public function dispatchSync($command, $handler = null) {
        if ($this->queueResolver
            && $this->commandShouldBeQueued($command)
            && method_exists($command, 'onConnection')
        ) {
            return $this->dispatchToQueue($command->onConnection('sync'));
        }

        return $this->dispatchNow($command, $handler);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param mixed $command
     * @param mixed $handler
     *
     * @return mixed
     */
    public function dispatchNow($command, $handler = null) {
        $uses = c::classUsesRecursive($command);

        if (in_array(CQueue_Trait_InteractsWithQueue::class, $uses)
            && in_array(CQueue_Trait_QueueableTrait::class, $uses)
            && !$command->job
        ) {
            $command->setJob(new CQueue_Job_SyncJob($this->container, json_encode([]), 'sync', 'sync'));
        }

        if ($handler || $handler = $this->getCommandHandler($command)) {
            $callback = function ($command) use ($handler) {
                $method = method_exists($handler, 'execute') ? 'execute' : (method_exists($handler, 'handle') ? 'handle' : '__invoke');

                return $handler->{$method}($command);
            };
        } else {
            $callback = function ($command) {
                $method = method_exists($command, 'execute') ? 'execute' : (method_exists($command, 'handle') ? 'handle' : '__invoke');

                return c::container()->call([$command, $method]);
            };
        }

        return $this->pipeline->send($command)->through($this->pipes)->then($callback);
    }

    /**
     * Attempt to find the batch with the given ID.
     *
     * @param string $batchId
     *
     * @return null|\CQueue_Batch
     */
    public function findBatch($batchId) {
        return CQueue::batchRepository()->find($batchId);
    }

    /**
     * Create a new batch of queueable jobs.
     *
     * @param \CCollection|array|mixed $jobs
     *
     * @return \CQueue_PendingBatch
     */
    public function batch($jobs) {
        return new CQueue_PendingBatch(CCollection::wrap($jobs));
    }

    /**
     * Create a new chain of queueable jobs.
     *
     * @param \CCollection|array $jobs
     *
     * @return \CQueue_PendingChain
     */
    public function chain($jobs) {
        $jobs = CCollection::wrap($jobs);

        return new CQueue_PendingChain($jobs->shift(), $jobs->toArray());
    }

    /**
     * Determine if the given command has a handler.
     *
     * @param mixed $command
     *
     * @return bool
     */
    public function hasCommandHandler($command) {
        return array_key_exists(get_class($command), $this->handlers);
    }

    /**
     * Retrieve the handler for a command.
     *
     * @param mixed $command
     *
     * @return bool|mixed
     */
    public function getCommandHandler($command) {
        if ($this->hasCommandHandler($command)) {
            return c::container()->make($this->handlers[get_class($command)]);
        }

        return false;
    }

    /**
     * Determine if the given command should be queued.
     *
     * @param mixed $command
     *
     * @return bool
     */
    protected function commandShouldBeQueued($command) {
        return $command instanceof CQueue_ShouldQueueInterface;
    }

    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param mixed $command
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function dispatchToQueue($command) {
        $connection = $command->connection ? $command->connection : null;
        $queue = call_user_func($this->queueResolver, $connection);

        if (!$queue instanceof CQueue_QueueInterface) {
            throw new RuntimeException('Queue resolver did not return a Queue implementation.');
        }
        if (method_exists($command, 'queue')) {
            return $command->queue($queue, $command);
        }

        return $this->pushCommandToQueue($queue, $command);
    }

    /**
     * Push the command onto the given queue instance.
     *
     * @param CQueue_QueueInterface $queue
     * @param mixed                 $command
     *
     * @return mixed
     */
    protected function pushCommandToQueue($queue, $command) {
        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn($command->queue, $command->delay, $command);
        }
        if (isset($command->queue)) {
            return $queue->pushOn($command->queue, $command);
        }
        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        return $queue->push($command);
    }

    /**
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @param mixed $command
     * @param mixed $handler
     *
     * @return void
     */
    public function dispatchAfterResponse($command, $handler = null) {
        if (!$this->allowsDispatchingAfterResponses) {
            $this->dispatchSync($command);

            return;
        }

        CF::terminating(function () use ($command, $handler) {
            $this->dispatchNow($command, $handler);
        });
    }

    /**
     * Set the pipes through which commands should be piped before dispatching.
     *
     * @param array $pipes
     *
     * @return $this
     */
    public function pipeThrough(array $pipes) {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Map a command to a handler.
     *
     * @param array $map
     *
     * @return $this
     */
    public function map(array $map) {
        $this->handlers = array_merge($this->handlers, $map);

        return $this;
    }

    /**
     * Allow dispatching after responses.
     *
     * @return $this
     */
    public function withDispatchingAfterResponses() {
        $this->allowsDispatchingAfterResponses = true;

        return $this;
    }

    /**
     * Disable dispatching after responses.
     *
     * @return $this
     */
    public function withoutDispatchingAfterResponses() {
        $this->allowsDispatchingAfterResponses = false;

        return $this;
    }
}
