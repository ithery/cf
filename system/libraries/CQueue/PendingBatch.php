<?php

class CQueue_PendingBatch {
    /**
     * The batch name.
     *
     * @var string
     */
    public $name = '';

    /**
     * The Org Id.
     *
     * @var int
     */
    public $orgId = null;

    /**
     * The jobs that belong to the batch.
     *
     * @var \CCollection
     */
    public $jobs;

    /**
     * The batch options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Create a new pending batch instance.
     *
     * @param \CCollection $jobs
     * @param null|int     $orgId
     *
     * @return void
     */
    public function __construct(CCollection $jobs, $orgId = null) {
        $this->jobs = $jobs;
        if (func_num_args() == 1) {
            $orgId = CF::orgId();
        }
        $this->orgId = $orgId;
    }

    /**
     * Add jobs to the batch.
     *
     * @param iterable|object|array $jobs
     *
     * @return $this
     */
    public function add($jobs) {
        $jobs = is_iterable($jobs) ? $jobs : carr::wrap($jobs);

        foreach ($jobs as $job) {
            $this->jobs->push($job);
        }

        return $this;
    }

    /**
     * Add a callback to be executed after all jobs in the batch have executed successfully.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function then($callback) {
        $this->options['then'][] = $callback instanceof Closure
                        ? new CQueue_SerializableClosure($callback)
                        : $callback;

        return $this;
    }

    /**
     * Get the "then" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function thenCallbacks() {
        return carr::get($this->options, 'then', []);
    }

    /**
     * Add a callback to be executed after the first failing job in the batch.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function catch($callback) {
        $this->options['catch'][] = $callback instanceof Closure
                    ? new CQueue_SerializableClosure($callback)
                    : $callback;

        return $this;
    }

    /**
     * Get the "catch" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function catchCallbacks() {
        return carr::get($this->options, 'catch', []);
    }

    /**
     * Add a callback to be executed after the batch has finished executing.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function finally($callback) {
        $this->options['finally'][] = $callback instanceof Closure
                    ? new CQueue_SerializableClosure($callback)
                    : $callback;

        return $this;
    }

    /**
     * Get the "finally" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function finallyCallbacks() {
        return carr::get($this->options, 'finally', []);
    }

    /**
     * Indicate that the batch should not be cancelled when a job within the batch fails.
     *
     * @param bool $allowFailures
     *
     * @return $this
     */
    public function allowFailures($allowFailures = true) {
        $this->options['allowFailures'] = $allowFailures;

        return $this;
    }

    /**
     * Determine if the pending batch allows jobs to fail without cancelling the batch.
     *
     * @return bool
     */
    public function allowsFailures() {
        return carr::get($this->options, 'allowFailures', false) === true;
    }

    /**
     * Set the name for the batch.
     *
     * @param string $name
     *
     * @return $this
     */
    public function name($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Specify the queue connection that the batched jobs should run on.
     *
     * @param string $connection
     *
     * @return $this
     */
    public function onConnection($connection) {
        $this->options['connection'] = $connection;

        return $this;
    }

    /**
     * Get the connection used by the pending batch.
     *
     * @return null|string
     */
    public function connection() {
        return carr::get($this->options, 'connection');
    }

    /**
     * Specify the queue that the batched jobs should run on.
     *
     * @param string $queue
     *
     * @return $this
     */
    public function onQueue($queue) {
        $this->options['queue'] = $queue;

        return $this;
    }

    /**
     * Get the queue used by the pending batch.
     *
     * @return null|string
     */
    public function queue() {
        return carr::get($this->options, 'queue');
    }

    /**
     * Add additional data into the batch's options array.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function withOption($key, $value) {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Dispatch the batch.
     *
     * @throws \Throwable
     *
     * @return \CQueue_Batch
     */
    public function dispatch() {
        $repository = CQueue::batchRepository();

        try {
            $batch = $repository->store($this);
            $batch = $batch->add($this->jobs);
        } catch (Exception $e) {
            if (isset($batch)) {
                $repository->delete($batch->id);
            }

            throw $e;
        }
        CEvent::dispatch(new CQueue_Event_BatchDispatched($batch));

        return $batch;
    }

    /**
     * Dispatch the batch after the response is sent to the browser.
     *
     * @return \CQueue_Batch
     */
    public function dispatchAfterResponse() {
        $repository = CQueue::batchRepository();

        $batch = $repository->store($this);

        if ($batch) {
            CF::terminating(function () use ($batch) {
                $this->dispatchExistingBatch($batch);
            });
        }

        return $batch;
    }

    /**
     * Dispatch an existing batch.
     *
     * @param \CQueue_Batch $batch
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function dispatchExistingBatch($batch) {
        try {
            $batch = $batch->add($this->jobs);
        } catch (Throwable $e) {
            if (isset($batch)) {
                $batch->delete();
            }

            throw $e;
        } catch (Exception $e) {
            if (isset($batch)) {
                $batch->delete();
            }

            throw $e;
        }

        CEvent::dispatch(new CQueue_Event_BatchDispatched($batch));
    }
}
