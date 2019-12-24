<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 3:04:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CQueue_Trait_QueueableTrait {

    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * The name of the connection the chain should be sent to.
     *
     * @var string|null
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var string|null
     */
    public $chainQueue;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|int|null
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
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection) {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function onQueue($queue) {
        $this->queue = $queue;
        return $this;
    }

    /**
     * Set the desired connection for the chain.
     *
     * @param  string|null  $connection
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
     * @param  string|null  $queue
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
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
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
     * @param  array|object
     * @return $this
     */
    public function through($middleware) {
        $this->middleware = carr::wrap($middleware);
        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return $this
     */
    public function chain($chain) {
        $this->chained = CF::collect($chain)->map(function ($job) {
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
            
            CEvent::dispatcher()->dispatch(CF::tap(unserialize(array_shift($this->chained)), function ($next) {
                        $next->chained = $this->chained;
                        $next->onConnection($next->connection ?: $this->chainConnection);
                        $next->onQueue($next->queue ?: $this->chainQueue);
                        $next->chainConnection = $this->chainConnection;
                        $next->chainQueue = $this->chainQueue;
                    }));
        }
    }

}
