<?php

class CEvent_QueuedClosure {
    /**
     * The underlying Closure.
     *
     * @var \Closure
     */
    public $closure;

    /**
     * The name of the connection the job should be sent to.
     *
     * @var null|string
     */
    public $connection;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var null|string
     */
    public $queue;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var null|\DateTimeInterface|\DateInterval|int
     */
    public $delay;

    /**
     * All of the "catch" callbacks for the queued closure.
     *
     * @var array
     */
    public $catchCallbacks = [];

    /**
     * Create a new queued closure event listener resolver.
     *
     * @param \Closure $closure
     *
     * @return void
     */
    public function __construct(Closure $closure) {
        $this->closure = $closure;
    }

    /**
     * Set the desired connection for the job.
     *
     * @param null|string $connection
     *
     * @return $this
     */
    public function onConnection($connection) {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param null|string $queue
     *
     * @return $this
     */
    public function onQueue($queue) {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired delay in seconds for the job.
     *
     * @param null|\DateTimeInterface|\DateInterval|int $delay
     *
     * @return $this
     */
    public function delay($delay) {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Specify a callback that should be invoked if the queued listener job fails.
     *
     * @param \Closure $closure
     *
     * @return $this
     */
    public function catch(Closure $closure) {
        $this->catchCallbacks[] = $closure;

        return $this;
    }

    /**
     * Resolve the actual event listener callback.
     *
     * @return \Closure
     */
    public function resolve() {
        return function (...$arguments) {
            c::dispatch(new CEvent_CallQueuedListener(CEvent_InvokeQueuedClosure::class, 'handle', [
                'closure' => new CFunction_SerializableClosure($this->closure),
                'arguments' => $arguments,
                'catch' => c::collect($this->catchCallbacks)->map(function ($callback) {
                    return new CFunction_SerializableClosure($callback);
                })->all(),
            ]))->onConnection($this->connection)->onQueue($this->queue)->delay($this->delay);
        };
    }
}
