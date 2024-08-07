<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CQueue_AbstractQueue implements CQueue_QueueInterface {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The IoC container instance.
     *
     * @var CContainer_Container
     */
    protected $container;

    /**
     * The connection name for the queue.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * Indicates that jobs should be dispatched after all database transactions have committed.
     *
     * @return $this
     */
    protected $dispatchAfterCommit;

    /**
     * The create payload callbacks.
     *
     * @var callable[]
     */
    protected static $createPayloadCallbacks = [];

    /**
     * Push a new job onto the queue.
     *
     * @param string $queue
     * @param string $job
     * @param mixed  $data
     *
     * @return mixed
     */
    public function pushOn($queue, $job, $data = '') {
        return $this->push($job, $data, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param string                               $queue
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $job
     * @param mixed                                $data
     *
     * @return mixed
     */
    public function laterOn($queue, $delay, $job, $data = '') {
        return $this->later($delay, $job, $data, $queue);
    }

    /**
     * Push an array of jobs onto the queue.
     *
     * @param array       $jobs
     * @param mixed       $data
     * @param null|string $queue
     *
     * @return void
     */
    public function bulk($jobs, $data = '', $queue = null) {
        foreach ((array) $jobs as $job) {
            $this->push($job, $data, $queue);
        }
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string|object $job
     * @param string        $queue
     * @param mixed         $data
     *
     * @throws \CQueue_Exception_InvalidPayloadException
     *
     * @return string
     */
    protected function createPayload($job, $queue, $data = '') {
        if ($job instanceof Closure) {
            $job = CQueue_CallQueuedClosure::create($job);
        }

        $payload = json_encode($this->createPayloadArray($job, $queue, $data), \JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CQueue_Exception_InvalidPayloadException(
                'Unable to JSON encode payload. Error code: ' . json_last_error()
            );
        }

        return $payload;
    }

    /**
     * Create a payload array from the given job and data.
     *
     * @param string|object $job
     * @param string        $queue
     * @param mixed         $data
     *
     * @return array
     */
    protected function createPayloadArray($job, $queue, $data = '') {
        return is_object($job)
            ? $this->createObjectPayload($job, $queue)
            : $this->createStringPayload($job, $queue, $data);
    }

    /**
     * Create a payload for an object-based queue handler.
     *
     * @param object $job
     * @param string $queue
     *
     * @return array
     */
    protected function createObjectPayload($job, $queue) {
        $payload = $this->withCreatePayloadHooks($queue, [
            'uuid' => (string) cstr::uuid(),
            'displayName' => $this->getDisplayName($job),
            'job' => 'CQueue_CallQueuedHandler@call',
            'maxTries' => isset($job->tries) ? $job->tries : null,
            'maxExceptions' => isset($job->maxExceptions) ? ($job->maxExceptions ?: false) : false,
            'failOnTimeout' => isset($job->failOnTimeout) ? ($job->failOnTimeout ?: false) : false,
            'backoff' => $this->getJobBackoff($job),
            'timeout' => isset($job->timeout) ? $job->timeout : null,
            'retryUntil' => $this->getJobExpiration($job),
            'data' => [
                'commandName' => $job,
                'command' => $job,
            ],
        ]);
        $command = $this->jobShouldBeEncrypted($job)
                    ? CCrypt::encrypter()->encrypt(serialize(clone $job))
                    : serialize(clone $job);

        return array_merge($payload, [
            'data' => array_merge($payload['data'], [
                'commandName' => get_class($job),
                'command' => $command,
            ]),
        ]);
    }

    /**
     * Get the display name for the given job.
     *
     * @param object $job
     *
     * @return string
     */
    protected function getDisplayName($job) {
        return method_exists($job, 'displayName') ? $job->displayName() : get_class($job);
    }

    /**
     * Get the backoff for an object-based queue handler.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    public function getJobBackoff($job) {
        if (!method_exists($job, 'backoff') && !isset($job->backoff)) {
            return;
        }

        if (is_null($backoff = (isset($job->backoff) ? $job->backoff : $job->backoff()))) {
            return;
        }

        return c::collect(carr::wrap($backoff))
            ->map(function ($backoff) {
                return $backoff instanceof DateTimeInterface
                    ? $this->secondsUntil($backoff)
                    : $backoff;
            })->implode(',');
    }

    /**
     * Get the expiration timestamp for an object-based queue handler.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    public function getJobExpiration($job) {
        if (!method_exists($job, 'retryUntil') && !isset($job->retryUntil)) {
            return;
        }

        $expiration = isset($job->retryUntil) ? $job->retryUntil : $job->retryUntil();

        return $expiration instanceof DateTimeInterface
                        ? $expiration->getTimestamp() : $expiration;
    }

    /**
     * Determine if the job should be encrypted.
     *
     * @param object $job
     *
     * @return bool
     */
    protected function jobShouldBeEncrypted($job) {
        if ($job instanceof CQueue_Contract_ShouldBeEncryptedInterface) {
            return true;
        }

        return isset($job->shouldBeEncrypted) && $job->shouldBeEncrypted;
    }

    /**
     * Create a typical, string based queue payload array.
     *
     * @param string $job
     * @param string $queue
     * @param mixed  $data
     *
     * @return array
     */
    protected function createStringPayload($job, $queue, $data) {
        return $this->withCreatePayloadHooks($queue, [
            'uuid' => (string) cstr::uuid(),
            'displayName' => is_string($job) ? explode('@', $job)[0] : null,
            'job' => $job,
            'maxTries' => null,
            'maxExceptions' => null,
            'failOnTimeout' => false,
            'backoff' => null,
            'timeout' => null,
            'data' => $data,
        ]);
    }

    /**
     * Register a callback to be executed when creating job payloads.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function createPayloadUsing($callback) {
        if (is_null($callback)) {
            static::$createPayloadCallbacks = [];
        } else {
            static::$createPayloadCallbacks[] = $callback;
        }
    }

    /**
     * Create the given payload using any registered payload hooks.
     *
     * @param string $queue
     * @param array  $payload
     *
     * @return array
     */
    protected function withCreatePayloadHooks($queue, array $payload) {
        if (!empty(static::$createPayloadCallbacks)) {
            foreach (static::$createPayloadCallbacks as $callback) {
                $payload = array_merge($payload, call_user_func(
                    $callback,
                    $this->getConnectionName(),
                    $queue,
                    $payload
                ));
            }
        }

        return $payload;
    }

    /**
     * Enqueue a job using the given callback.
     *
     * @param \Closure|string|object                    $job
     * @param string                                    $payload
     * @param string                                    $queue
     * @param null|\DateTimeInterface|\DateInterval|int $delay
     * @param callable                                  $callback
     *
     * @return mixed
     */
    protected function enqueueUsing($job, $payload, $queue, $delay, $callback) {
        if ($this->shouldDispatchAfterCommit($job)) {
            $transactionManager = CDatabase::transactionManager();

            return $transactionManager->addCallback(
                function () use ($payload, $queue, $delay, $callback, $job) {
                    return c::tap($callback($payload, $queue, $delay), function ($jobId) use ($job) {
                        $this->raiseJobQueuedEvent($jobId, $job);
                    });
                }
            );
        }

        return c::tap($callback($payload, $queue, $delay), function ($jobId) use ($job) {
            $this->raiseJobQueuedEvent($jobId, $job);
        });
    }

    /**
     * Determine if the job should be dispatched after all database transactions have committed.
     *
     * @param \Closure|string|object $job
     *
     * @return bool
     */
    protected function shouldDispatchAfterCommit($job) {
        if (is_object($job) && isset($job->afterCommit)) {
            return $job->afterCommit;
        }

        if (isset($this->dispatchAfterCommit)) {
            return $this->dispatchAfterCommit;
        }

        return false;
    }

    /**
     * Raise the job queued event.
     *
     * @param null|string|int        $jobId
     * @param \Closure|string|object $job
     *
     * @return void
     */
    protected function raiseJobQueuedEvent($jobId, $job) {
        CEvent::dispatcher()->dispatch(new CQueue_Event_JobQueued($this->connectionName, $jobId, $job));
    }

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName() {
        return $this->connectionName;
    }

    /**
     * Set the connection name for the queue.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setConnectionName($name) {
        $this->connectionName = $name;

        return $this;
    }

    /**
     * Set the IoC container instance.
     *
     * @param CContainer_Container $container
     *
     * @return void
     */
    public function setContainer(CContainer_Container $container) {
        $this->container = $container;
    }
}
