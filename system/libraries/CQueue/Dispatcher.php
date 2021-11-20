<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 2:49:31 AM
 */
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

                return $this->container->call([$command, $method]);
            };
        }

        return $this->pipeline->send($command)->through($this->pipes)->then($callback);

        if ($handler || $handler = $this->getCommandHandler($command)) {
            $callback = function ($command) use ($handler) {
                return $handler->handle($command);
            };
        } else {
            $callback = function ($command) {
                return $this->container->call([$command, 'execute']);
            };
        }

        return $this->pipeline->send($command)->through($this->pipes)->then($callback);
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
            return $this->container->make($this->handlers[get_class($command)]);
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
}
