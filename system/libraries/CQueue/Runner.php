<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 4:53:31 AM
 */
class CQueue_Runner {
    /**
     * The queue worker instance.
     *
     * @var CQueue_Worker
     */
    protected $worker;

    /**
     * The cache store implementation.
     *
     * @var CCache_Repository
     */
    protected $cache;

    protected static $listenedForEvents = false;

    /**
     * The options for worker setting.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new queue work command.
     *
     * @param CQueue_Worker     $worker
     * @param CCache_Repository $cache
     * @param array             $options
     *
     * @return void
     */
    public function __construct(CQueue_Worker $worker, CCache_Repository $cache = null, array $options = []) {
        $this->cache = $cache;
        $this->worker = $worker;
        $this->options = $options;
    }

    public function run($connection = null, $queue = null) {
        if ($connection == null) {
            $connection = CQueue::config('default');
        }

        if ($this->downForMaintenance() && $this->getOption('once')) {
            return $this->worker->sleep($this->getOption('sleep'));
        }

        // We'll listen to the processed and failed events so we can write information
        // to the console as jobs are processed, which will let the developer watch
        // which jobs are coming through a queue and be informed on its progress.
        $this->listenForEvents();
        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue($connection);
        $this->runWorker(
            $connection,
            $queue
        );
    }

    /**
     * Run the worker instance.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return array
     */
    protected function runWorker($connection, $queue) {
        $this->worker->setCache($this->cache);

        return $this->worker->{$this->getOption('once') ? 'runNextJob' : 'daemon'}(
            $connection,
            $queue,
            $this->gatherWorkerOptions()
        );
    }

    protected function runDaemon($connection, $queue) {
        return $this->worker->daemon($connection, $queue, $this->gatherWorkerOptions());
    }

    protected function gatherWorkerOptions() {
        return new CQueue_WorkerOptions(
            $this->getOption('name'),
            max($this->getOption('backoff'), $this->getOption('delay')),
            $this->getOption('memory'),
            $this->getOption('timeout'),
            $this->getOption('sleep'),
            $this->getOption('maxTries'),
            $this->getOption('force'),
            $this->getOption('stopWhenEmpty'),
            $this->getOption('maxJobs'),
            $this->getOption('maxTime'),
            $this->getOption('rest')
        );
        //return new CQueue_WorkerOptions();
    }

    /**
     * Determine if the worker should run in maintenance mode.
     *
     * @return bool
     */
    protected function downForMaintenance() {
        return $this->getOption('force') ? false : CF::isDownForMaintenance();
    }

    /**
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    protected function listenForEvents() {
        if (!static::$listenedForEvents) {
            CEvent::dispatcher()->listen(CQueue_Event_JobProcessing::class, function ($event) {
                $this->writeOutput($event->job, 'starting');
            });
            CEvent::dispatcher()->listen(CQueue_Event_JobProcessed::class, function ($event) {
                $this->writeOutput($event->job, 'success');
            });
            CEvent::dispatcher()->listen(CQueue_Event_JobFailed::class, function ($event) {
                $this->writeOutput($event->job, 'failed');
                $this->logFailedJob($event);
            });
            static::$listenedForEvents = true;
        }
    }

    /**
     * Get the queue name for the worker.
     *
     * @param string $connection
     *
     * @return string
     */
    protected function getQueue($connection) {
        return $this->getOption('queue') ?: CQueue::config("connections.{$connection}.queue", 'default');
    }

    /**
     * Write the status output for the queue worker.
     *
     * @param CQueue_AbstractJob $job
     * @param string             $status
     *
     * @return void
     */
    protected function writeOutput(CQueue_AbstractJob $job, $status) {
        switch ($status) {
            case 'starting':
                return $this->writeStatus($job, 'Processing', 'comment');
            case 'success':
                return $this->writeStatus($job, 'Processed', 'info');
            case 'failed':
                return $this->writeStatus($job, 'Failed', 'error');
        }
    }

    /**
     * Format the status output for the queue worker.
     *
     * @param CQueue_AbstractJob $job
     * @param string             $status
     * @param string             $type
     *
     * @return void
     */
    protected function writeStatus(CQueue_AbstractJob $job, $status, $type) {
        //        $this->output->writeln(sprintf(
        //                        "<{$type}>[%s][%s] %s</{$type}> %s", Carbon::now()->format('Y-m-d H:i:s'), $job->getJobId(), str_pad("{$status}:", 11), $job->resolveName()
        //        ));

        $message = sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s",
            CCarbon::now()->format('Y-m-d H:i:s'),
            $job->getJobId(),
            str_pad("{$status}:", 11),
            $job->resolveName()
        );
        if (CDaemon::getRunningService() != null) {
            CDaemon::log($message);
        } else {
            echo $message;
        }
    }

    /**
     * Store a failed job event.
     *
     * @param CQueue_Event_JobFailed $event
     *
     * @return void
     */
    protected function logFailedJob(CQueue_Event_JobFailed $event) {
        CQueue_FailerFactory::getFailerInstance()->log(
            $event->connectionName,
            $event->job->getQueue(),
            $event->job->getRawBody(),
            $event->exception
        );
    }

    public function setOption($name, $value) {
        $this->options[$name] = $value;

        return $this;
    }

    protected function getOption($name) {
        $defaultOptions = [];
        //The name of the worker
        $defaultOptions['name'] = 'default';
        //The number of seconds to delay failed jobs (Deprecated)
        $defaultOptions['delay'] = 0;
        //The number of seconds to wait before retrying a job that encountered an uncaught exception
        $defaultOptions['backoff'] = 0;
        //The number of jobs to process before stopping
        $defaultOptions['maxJobs'] = 0;
        //The maximum number of seconds the worker should run
        $defaultOptions['maxTime'] = 0;
        //The memory limit in megabytes
        $defaultOptions['memory'] = 1024;
        //The number of seconds a child process can run
        $defaultOptions['timeout'] = 300;
        //Force the worker to run even in maintenance mode
        $defaultOptions['force'] = false;
        //Number of seconds to sleep when no job is available
        $defaultOptions['sleep'] = 3;
        //Number of times to attempt a job before logging it failed
        $defaultOptions['maxTries'] = 1;
        //Stop when the queue is empty
        $defaultOptions['stopWhenEmpty'] = false;
        //Only process the next job on the queue
        $defaultOptions['once'] = true;
        $options = array_merge($defaultOptions, $this->options);

        return carr::get($options, $name);
    }

    public function getCurrentJobName() {
        return $this->worker->getCurrentJobName();
    }
}
