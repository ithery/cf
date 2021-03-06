<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 3:04:00 AM
 */
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
     * The number of seconds before the job should be made available.
     *
     * @var null|\DateTimeInterface|\DateInterval|int
     */
    public $delay;

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
            return serialize($job);
        })->all();

        return $this;
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
            }));
        }
    }
}
