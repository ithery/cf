<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CQueue_Trait_QueueableTrait {
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
     * The name of the connection the chain should be sent to.
     *
     * @var null|string
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var null|string
     */
    public $chainQueue;

    /**
     * The callbacks to be executed on chain failure.
     *
     * @var null|array
     */
    public $chainCatchCallbacks;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|array|int
     */
    public $delay;

    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var null|bool
     */
    public $afterCommit;

    /**
     * The middleware the job should be dispatched through.
     */
    public $middleware = [];

    /**
     * The jobs that should run if this job is successful.
     *
     * @var array
     */
    public $chained = [];

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
     * Set the desired connection for the chain.
     *
     * @param null|string $connection
     *
     * @return $this
     */
    public function allOnConnection($connection) {
        $this->chainConnection = $connection;
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the chain.
     *
     * @param null|string $queue
     *
     * @return $this
     */
    public function allOnQueue($queue) {
        $this->chainQueue = $queue;
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired delay for the job.
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
     * Set the delay for the job to zero seconds.
     *
     * @return $this
     */
    public function withoutDelay() {
        $this->delay = 0;

        return $this;
    }

    /**
     * Indicate that the job should be dispatched after all database transactions have committed.
     *
     * @return $this
     */
    public function afterCommit() {
        $this->afterCommit = true;

        return $this;
    }

    /**
     * Indicate that the job should not wait until database transactions have been committed before dispatching.
     *
     * @return $this
     */
    public function beforeCommit() {
        $this->afterCommit = false;

        return $this;
    }

    /**
     * Get the middleware the job should be dispatched through.
     *
     * @return array
     */
    public function middleware() {
        return $this->middleware ?: [];
    }

    /**
     * Specify the middleware the job should be dispatched through.
     *
     * @param mixed $middleware
     *
     * @return $this
     */
    public function through($middleware) {
        $this->middleware = carr::wrap($middleware);

        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function chain($chain) {
        $this->chained = c::collect($chain)->map(function ($job) {
            return $this->serializeJob($job);
        })->all();

        return $this;
    }

    /**
     * Prepend a job to the current chain so that it is run after the currently running job.
     *
     * @param mixed $job
     *
     * @return $this
     */
    public function prependToChain($job) {
        $this->chained = carr::prepend($this->chained, $this->serializeJob($job));

        return $this;
    }

    /**
     * Append a job to the end of the current chain.
     *
     * @param mixed $job
     *
     * @return $this
     */
    public function appendToChain($job) {
        $this->chained = array_merge($this->chained, [$this->serializeJob($job)]);

        return $this;
    }

    /**
     * Serialize a job for queuing.
     *
     * @param mixed $job
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function serializeJob($job) {
        if ($job instanceof Closure) {
            $job = CQueue_CallQueuedClosure::create($job);
        }

        return serialize($job);
    }

    /**
     * Dispatch the next job on the chain.
     *
     * @return void
     */
    public function dispatchNextJobInChain() {
        if (!empty($this->chained)) {
            new CQueue_PendingDispatch(c::tap(unserialize(array_shift($this->chained)), function ($next) {
                $next->chained = $this->chained;

                $next->onConnection($next->connection ?: $this->chainConnection);
                $next->onQueue($next->queue ?: $this->chainQueue);

                $next->chainConnection = $this->chainConnection;
                $next->chainQueue = $this->chainQueue;
                $next->chainCatchCallbacks = $this->chainCatchCallbacks;
            }));
        }
    }

    /**
     * Invoke all of the chain's failed job callbacks.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function invokeChainCatchCallbacks($e) {
        c::collect($this->chainCatchCallbacks)->each(function ($callback) use ($e) {
            $callback($e);
        });
    }
}
