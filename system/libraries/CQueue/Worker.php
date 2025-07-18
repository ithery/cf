<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Worker {
    use CDatabase_Trait_DetectLostConnection;

    const EXIT_SUCCESS = 0;

    const EXIT_ERROR = 1;

    const EXIT_MEMORY_LIMIT = 12;

    /**
     * Indicates if the worker should exit.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    public $paused = false;

    /**
     * The name of the worker.
     *
     * @var string
     */
    protected $name;

    /**
     * The queue manager instance.
     *
     * @var \CQueue_FactoryInterface
     */
    protected $manager;

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
     *
     * @var CCache_Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
     *
     * @var \CException_ExceptionHandler
     */
    protected $exceptions;

    /**
     * The callback used to determine if the application is in maintenance mode.
     *
     * @var \callable
     */
    protected $isDownForMaintenance;

    /**
     * The callback used to reset the application's scope.
     *
     * @var callable
     */
    protected $resetScope;

    /**
     * The callbacks used to pop jobs from queues.
     *
     * @var callable[]
     */
    protected static $popCallbacks = [];

    /**
     * @var string
     */
    protected $currentJobName;

    /**
     * Create a new queue worker.
     *
     * @param CQueue_Manager              $manager
     * @param CEvent_Dispatcher           $events
     * @param CException_ExceptionHandler $exceptions
     * @param \callable                   $isDownForMaintenance
     *
     * @return void
     */
    public function __construct(CQueue_Manager $manager, CEvent_Dispatcher $events, CException_ExceptionHandler $exceptions, callable $isDownForMaintenance, callable $resetScope = null) {
        $this->events = $events;
        $this->manager = $manager;
        $this->exceptions = $exceptions;
        $this->isDownForMaintenance = $isDownForMaintenance;
        $this->resetScope = $resetScope;
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param string                $connectionName
     * @param string                $queue
     * @param \CQueue_WorkerOptions $options
     *
     * @return void
     */
    public function daemon($connectionName, $queue, CQueue_WorkerOptions $options) {
        if ($supportsAsyncSignals = $this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }
        $lastRestart = $this->getTimestampOfLastQueueRestart();
        list($startTime, $jobsProcessed) = [hrtime(true) / 1e9, 0];
        while (true) {
            // Before reserving any jobs, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
            if (!$this->daemonShouldRun($options, $connectionName, $queue)) {
                $status = $this->pauseWorker($options, $lastRestart);
                if (!is_null($status)) {
                    return $this->stop($status);
                }

                continue;
            }
            if (isset($this->resetScope)) {
                call_user_func($this->resetScope);
            }

            // First, we will attempt to get the next job off of the queue. We will also
            // register the timeout handler and reset the alarm for this job so it is
            // not stuck in a frozen state forever. Then, we can fire off this job.
            $job = $this->getNextJob(
                $this->manager->connection($connectionName),
                $queue
            );
            if ($supportsAsyncSignals) {
                $this->registerTimeoutHandler($job, $options);
            }
            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this job for processing. Otherwise, we will need to sleep the
            // worker so no more jobs are processed until they should be processed.
            if ($job) {
                $jobsProcessed++;
                $this->runJob($job, $connectionName, $options);
                if ($options->rest > 0) {
                    $this->sleep($options->rest);
                }
            } else {
                $this->sleep($options->sleep);
            }

            if ($this->supportsAsyncSignals()) {
                $this->resetTimeoutHandler();
            }
            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
            $status = $this->stopIfNecessary(
                $options,
                $lastRestart,
                $startTime,
                $jobsProcessed,
                $job
            );

            if (!is_null($status)) {
                return $this->stop($status);
            }
        }
    }

    /**
     * Register the worker timeout handler.
     *
     * @param null|CQueue_AbstractJob $job
     * @param CQueue_WorkerOptions    $options
     *
     * @return void
     */
    protected function registerTimeoutHandler($job, CQueue_WorkerOptions $options) {
        // We will register a signal handler for the alarm signal so that we can kill this
        // process if it is running too long because it has frozen. This uses the async
        // signals supported in recent versions of PHP to accomplish it conveniently.
        pcntl_signal(SIGALRM, function () use ($job, $options) {
            if ($job) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $job->getConnectionName(),
                    $job,
                    (int) $options->maxTries,
                    $e = $this->maxAttemptsExceededException($job)
                );

                $this->markJobAsFailedIfWillExceedMaxExceptions(
                    $job->getConnectionName(),
                    $job,
                    $e
                );

                $this->markJobAsFailedIfItShouldFailOnTimeout(
                    $job->getConnectionName(),
                    $job,
                    $e
                );
            }
            $this->kill(static::EXIT_ERROR);
        });
        pcntl_alarm(
            max($this->timeoutForJob($job, $options), 0)
        );
    }

    /**
     * Reset the worker timeout handler.
     *
     * @return void
     */
    protected function resetTimeoutHandler() {
        pcntl_alarm(0);
    }

    /**
     * Get the appropriate timeout for the given job.
     *
     * @param null|CQueue_AbstractJob $job
     * @param CQueue_WorkerOptions    $options
     *
     * @return int
     */
    protected function timeoutForJob($job, CQueue_WorkerOptions $options) {
        return $job && !is_null($job->timeout()) ? $job->timeout() : $options->timeout;
    }

    /**
     * Determine if the daemon should process on this iteration.
     *
     * @param CQueue_WorkerOptions $options
     * @param string               $connectionName
     * @param string               $queue
     *
     * @return bool
     */
    protected function daemonShouldRun(CQueue_WorkerOptions $options, $connectionName, $queue) {
        //$isDownForMaintenance = $this->isDownForMaintenance();
        $isDownForMaintenance = CF::isDownForMaintenance();

        return !(($isDownForMaintenance && !$options->force)
                || $this->paused
                || $this->events->until(new CQueue_Event_Looping($connectionName, $queue)) === false);
    }

    /**
     * Pause the worker for the current loop.
     *
     * @param CQueue_WorkerOptions $options
     * @param int                  $lastRestart
     *
     * @return void
     */
    protected function pauseWorker(CQueue_WorkerOptions $options, $lastRestart) {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);
        $this->stopIfNecessary($options, $lastRestart);
    }

    /**
     * Stop the process if necessary.
     *
     * @param CQueue_WorkerOptions $options
     * @param int                  $lastRestart
     * @param mixed                $job
     * @param mixed                $startTime
     * @param mixed                $jobsProcessed
     */
    protected function stopIfNecessary(CQueue_WorkerOptions $options, $lastRestart, $startTime = 0, $jobsProcessed = 0, $job = null) {
        if ($this->shouldQuit) {
            $this->stop();
        } elseif ($this->memoryExceeded($options->memory)) {
            return static::EXIT_MEMORY_LIMIT;
        } elseif ($this->queueShouldRestart($lastRestart)) {
            return static::EXIT_SUCCESS;
        } elseif ($options->stopWhenEmpty && is_null($job)) {
            return static::EXIT_SUCCESS;
        } elseif ($options->maxTime && hrtime(true) / 1e9 - $startTime >= $options->maxTime) {
            return static::EXIT_SUCCESS;
        } elseif ($options->maxJobs && $jobsProcessed >= $options->maxJobs) {
            return static::EXIT_SUCCESS;
        }
    }

    /**
     * Process the next job on the queue.
     *
     * @param string               $connectionName
     * @param string               $queue
     * @param CQueue_WorkerOptions $options
     *
     * @return void
     */
    public function runNextJob($connectionName, $queue, CQueue_WorkerOptions $options) {
        $job = $this->getNextJob(
            $this->manager->connection($connectionName),
            $queue
        );

        // If we're able to pull a job off of the stack, we will process it and then return
        // from this method. If there is no job on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing jobs after sleep.
        if ($job) {
            return $this->runJob($job, $connectionName, $options);
        }
        $this->sleep($options->sleep);
    }

    /**
     * Get the next job from the queue connection.
     *
     * @param CQueue_AbstractQueue $connection
     * @param string               $queue
     *
     * @return null|CQueue_AbstractJob
     */
    protected function getNextJob($connection, $queue) {
        $popJobCallback = function ($queue) use ($connection) {
            return $connection->pop($queue);
        };

        try {
            if (isset(static::$popCallbacks[$this->name])) {
                return call_user_func_array(static::$popCallbacks[$this->name], [$popJobCallback, $queue]);
            }
            foreach (explode(',', $queue) as $queue) {
                if (!is_null($job = $popJobCallback($queue))) {
                    return $job;
                }
            }
        } catch (Throwable $e) {
            $this->exceptions->report($e);
            if (CDaemon::isDaemon()) {
                CDaemon::handleException($e);
            }
            $this->stopWorkerIfLostConnection($e);
            $this->sleep(1);
        }
    }

    /**
     * Process the given job.
     *
     * @param CQueue_AbstractJob   $job
     * @param string               $connectionName
     * @param CQueue_WorkerOptions $options
     *
     * @return void
     */
    protected function runJob($job, $connectionName, CQueue_WorkerOptions $options) {
        try {
            $this->currentJobName = $job->resolveName();

            return $this->process($connectionName, $job, $options);
        } catch (Throwable $e) {
            $this->currentJobName = null;
            if (CDaemon::getRunningService() != null) {
                CDaemon::log('Run Job Exception :' . $e->getMessage());
                if (!CF::isProduction()) {
                    CDaemon::log($e->getTraceAsString());
                }
            } else {
                $this->exceptions->report($e);
            }
            $this->stopWorkerIfLostConnection($e);
        } finally {
            $this->currentJobName = null;
        }
    }

    /**
     * Stop the worker if we have lost connection to a database.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    protected function stopWorkerIfLostConnection($e) {
        if ($this->causedByLostConnection($e)) {
            $this->shouldQuit = true;
        }
    }

    /**
     * Process the given job from the queue.
     *
     * @param string               $connectionName
     * @param CQueue_AbstractJob   $job
     * @param CQueue_WorkerOptions $options
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function process($connectionName, $job, CQueue_WorkerOptions $options) {
        try {
            // First we will raise the before job event and determine if the job has already ran
            // over its maximum attempt limits, which could primarily happen when this job is
            // continually timing out and not actually throwing any exceptions from itself.
            $this->raiseBeforeJobEvent($connectionName, $job);
            $this->markJobAsFailedIfAlreadyExceedsMaxAttempts(
                $connectionName,
                $job,
                (int) $options->maxTries
            );
            if ($job->isDeleted()) {
                return $this->raiseAfterJobEvent($connectionName, $job);
            }
            // Here we will fire off the job and let it process. We will catch any exceptions so
            // they can be reported to the developers logs, etc. Once the job is finished the
            // proper events will be fired to let any listeners know this job has finished.
            $job->fire();

            $this->raiseAfterJobEvent($connectionName, $job);
        } catch (Throwable $e) {
            $this->handleJobException($connectionName, $job, $options, $e);
        }
    }

    /**
     * Handle an exception that occurred while the job was running.
     *
     * @param string               $connectionName
     * @param CQueue_JobInterface  $job
     * @param CQueue_WorkerOptions $options
     * @param \Exception           $e
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function handleJobException($connectionName, $job, CQueue_WorkerOptions $options, $e) {
        try {
            // First, we will go ahead and mark the job as failed if it will exceed the maximum
            // attempts it is allowed to run the next time we process it. If so we will just
            // go ahead and mark it as failed now so we do not have to release this again.

            if (!$job->hasFailed()) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $connectionName,
                    $job,
                    (int) $options->maxTries,
                    $e
                );
                $this->markJobAsFailedIfWillExceedMaxExceptions(
                    $connectionName,
                    $job,
                    $e
                );
            }
            $this->raiseExceptionOccurredJobEvent(
                $connectionName,
                $job,
                $e
            );
        } finally {
            // If we catch an exception, we will attempt to release the job back onto the queue
            // so it is not lost entirely. This'll let the job be retried at a later time by
            // another listener (or this same one). We will re-throw this exception after.
            if (!$job->isDeleted() && !$job->isReleased() && !$job->hasFailed()) {
                $job->release($this->calculateBackoff($job, $options));
            }
            CEvent::dispatcher()->dispatch(new CQueue_Event_JobReleasedAfterException(
                $connectionName,
                $job
            ));
        }

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * This will likely be because the job previously exceeded a timeout.
     *
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     * @param int                $maxTries
     *
     * @return void
     */
    protected function markJobAsFailedIfAlreadyExceedsMaxAttempts($connectionName, $job, $maxTries) {
        $maxTries = !is_null($job->maxTries()) ? $job->maxTries() : $maxTries;
        $retryUntil = $job->retryUntil();
        if ($retryUntil && CCarbon::now()->getTimestamp() <= $retryUntil) {
            return;
        }

        if (!$retryUntil && ($maxTries === 0 || $job->attempts() <= $maxTries)) {
            return;
        }
        $this->failJob($job, $e = $this->maxAttemptsExceededException($job));

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     * @param int                $maxTries
     * @param \Exception         $e
     *
     * @return void
     */
    protected function markJobAsFailedIfWillExceedMaxAttempts($connectionName, $job, $maxTries, $e) {
        $maxTries = !is_null($job->maxTries()) ? $job->maxTries() : $maxTries;

        if ($job->retryUntil() && $job->retryUntil() <= CCarbon::now()->getTimestamp()) {
            $this->failJob($job, $e);
        }

        if (!$job->retryUntil() && $maxTries > 0 && $job->attempts() >= $maxTries) {
            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * @param string               $connectionName
     * @param \CQueue_JobInterface $job
     * @param \Throwable           $e
     *
     * @return void
     */
    protected function markJobAsFailedIfWillExceedMaxExceptions($connectionName, $job, $e) {
        if (!$this->cache || is_null($uuid = $job->uuid())
            || is_null($maxExceptions = $job->maxExceptions())
        ) {
            return;
        }

        if (!$this->cache->get('job-exceptions:' . $uuid)) {
            $this->cache->put('job-exceptions:' . $uuid, 0, CCarbon::now()->addDay());
        }

        if ($maxExceptions <= $this->cache->increment('job-exceptions:' . $uuid)) {
            $this->cache->forget('job-exceptions:' . $uuid);

            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed if it should fail on timeouts.
     *
     * @param string               $connectionName
     * @param \CQueue_JobInterface $job
     * @param \Throwable           $e
     *
     * @return void
     */
    protected function markJobAsFailedIfItShouldFailOnTimeout($connectionName, $job, $e) {
        if (method_exists($job, 'shouldFailOnTimeout') ? $job->shouldFailOnTimeout() : false) {
            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed and raise the relevant event.
     *
     * @param CQueue_AbstractJob $job
     * @param \Exception         $e
     *
     * @return void
     */
    protected function failJob($job, $e) {
        return $job->fail($e);
    }

    /**
     * Calculate the backoff for the given job.
     *
     * @param \CQueue_JobInterface  $job
     * @param \CQueue_WorkerOptions $options
     *
     * @return int
     */
    protected function calculateBackoff($job, CQueue_WorkerOptions $options) {
        $backoff = explode(
            ',',
            method_exists($job, 'backoff') && !is_null($job->backoff())
                        ? $job->backoff()
                        : $options->backoff
        );

        return (int) (isset($backoff[$job->attempts() - 1]) ? $backoff[$job->attempts() - 1] : c::last($backoff));
    }

    /**
     * Raise the before queue job event.
     *
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     *
     * @return void
     */
    protected function raiseBeforeJobEvent($connectionName, $job) {
        $this->events->dispatch(new CQueue_Event_JobProcessing(
            $connectionName,
            $job
        ));
    }

    /**
     * Raise the after queue job event.
     *
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     *
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, $job) {
        $this->events->dispatch(new CQueue_Event_JobProcessed(
            $connectionName,
            $job
        ));
    }

    /**
     * Raise the exception occurred queue job event.
     *
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     * @param \Exception         $e
     *
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent($connectionName, $job, $e) {
        $this->events->dispatch(new CQueue_Event_JobExceptionOccurred(
            $connectionName,
            $job,
            $e
        ));
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param null|int $lastRestart
     *
     * @return bool
     */
    protected function queueShouldRestart($lastRestart) {
        return $this->getTimestampOfLastQueueRestart() != $lastRestart;
    }

    /**
     * Get the last queue restart timestamp, or null.
     *
     * @return null|int
     */
    protected function getTimestampOfLastQueueRestart() {
        if ($this->cache) {
            return $this->cache->get('cresenity:queue:restart');
        }
    }

    /**
     * Enable async signals for the process.
     *
     * @return void
     */
    protected function listenForSignals() {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function () {
            $this->shouldQuit = true;
        });
        pcntl_signal(SIGUSR2, function () {
            $this->paused = true;
        });
        pcntl_signal(SIGCONT, function () {
            $this->paused = false;
        });
    }

    /**
     * Determine if "async" signals are supported.
     *
     * @return bool
     */
    protected function supportsAsyncSignals() {
        return extension_loaded('pcntl');
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param int $memoryLimit
     *
     * @return bool
     */
    public function memoryExceeded($memoryLimit) {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param int $status
     *
     * @return void
     */
    public function stop($status = 0) {
        $this->events->dispatch(new CQueue_Event_WorkerStopping($status));
        exit($status);
    }

    /**
     * Kill the process.
     *
     * @param int $status
     *
     * @return void
     */
    public function kill($status = 0) {
        $this->events->dispatch(new CQueue_Event_WorkerStopping($status));
        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }
        exit($status);
    }

    /**
     * Create an instance of MaxAttemptsExceededException.
     *
     * @param null|\CQueue_JobInterface $job
     *
     * @return CQueue_Exception_MaxAttemptsExceededException
     */
    protected function maxAttemptsExceededException($job) {
        return new CQueue_Exception_MaxAttemptsExceededException(
            $job->resolveName() . ' has been attempted too many times or run too long. The job may have previously timed out.'
        );
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param int|float $seconds
     *
     * @return void
     */
    public function sleep($seconds) {
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Set the cache repository implementation.
     *
     * @param CCache_Repository $cache
     *
     * @return $this
     */
    public function setCache(CCache_Repository $cache = null) {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Set the name of the worker.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Register a callback to be executed to pick jobs.
     *
     * @param string   $workerName
     * @param callable $callback
     *
     * @return void
     */
    public static function popUsing($workerName, $callback) {
        if (is_null($callback)) {
            unset(static::$popCallbacks[$workerName]);
        } else {
            static::$popCallbacks[$workerName] = $callback;
        }
    }

    /**
     * Get the queue manager instance.
     *
     * @return CQueue_Manager
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * Set the queue manager instance.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    public function setManager(CQueue_Manager $manager) {
        $this->manager = $manager;
    }

    public function getCurrentJobName() {
        return $this->currentJobName;
    }
}
