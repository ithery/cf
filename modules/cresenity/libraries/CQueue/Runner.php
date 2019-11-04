<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 4:53:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
     * Create a new queue work command.
     *
     * @param  CQueue_Worker  $worker
     * @param  CCache_Repository  $cache
     * @return void
     */
    public function __construct(CQueue_Worker $worker, CCache_Repository $cache = null) {
        $this->cache = $cache;
        $this->worker = $worker;
    }

    public function run() {
        if ($this->downForMaintenance() && $this->option('once')) {
            return $this->worker->sleep($this->option('sleep'));
        }
        // We'll listen to the processed and failed events so we can write information
        // to the console as jobs are processed, which will let the developer watch
        // which jobs are coming through a queue and be informed on its progress.
        $this->listenForEvents();
        $connection = CQueue::config('driver');
        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue($connection);
        $this->runWorker(
                $connection, $queue
        );
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return array
     */
    protected function runWorker($connection, $queue) {
        $this->worker->setCache($this->cache);
//        return $this->worker->{$this->option('once') ? 'runNextJob' : 'daemon'}(
//                        $connection, $queue, $this->gatherWorkerOptions()
        return $this->worker->runNextJob($connection, $queue, $this->gatherWorkerOptions());
    }

    protected function runDaemon($connection, $queue) {
        return $this->worker->daemon($connection, $queue, $this->gatherWorkerOptions());
    }

    protected function gatherWorkerOptions() {
        return new CQueue_WorkerOptions(
                $this->option('delay'), $this->option('memory'), $this->option('timeout'), $this->option('sleep'), $this->option('tries'), $this->option('force'), $this->option('stop-when-empty')
        );
        //return new CQueue_WorkerOptions();
    }

    /**
     * Determine if the worker should run in maintenance mode.
     *
     * @return bool
     */
    protected function downForMaintenance() {
        //return $this->option('force') ? false : $this->laravel->isDownForMaintenance();
        return false;
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
     * @param  string  $connection
     * @return string
     */
    protected function getQueue($connection) {
//        return $this->option('queue') ?: $this->laravel['config']->get(
//            "queue.connections.{$connection}.queue", 'default'
//        );
        return CQueue::config('queue');
    }

    /**
     * Write the status output for the queue worker.
     *
     * @param  CQueue_AbstractJob  $job
     * @param  string $status
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
     * @param  CQueue_AbstractJob  $job
     * @param  string  $status
     * @param  string  $type
     * @return void
     */
    protected function writeStatus(CQueue_AbstractJob $job, $status, $type) {
//        $this->output->writeln(sprintf(
//                        "<{$type}>[%s][%s] %s</{$type}> %s", Carbon::now()->format('Y-m-d H:i:s'), $job->getJobId(), str_pad("{$status}:", 11), $job->resolveName()
//        ));

        $message = sprintf(
                "<{$type}>[%s][%s] %s</{$type}> %s", CCarbon::now()->format('Y-m-d H:i:s'), $job->getJobId(), str_pad("{$status}:", 11), $job->resolveName()
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
     * @param  CQueue_Event_JobFailed  $event
     * @return void
     */
    protected function logFailedJob(CQueue_Event_JobFailed $event) {
        $queueFailer = new CQueue_FailedJob_DatabaseFailedJob(CDatabase::instance(CQueue::config('connection','default')), CQueue::config('tableFailed'));
        $queueFailer->log(
                $event->connectionName, $event->job->getQueue(), $event->job->getRawBody(), $event->exception
        );
    }

    protected function option($name) {
        $options = [];

        $options['delay'] = 0;
        $options['memory'] = 1024;
        $options['timeout'] = 300;
        $options['sleep'] = 0;
        $options['maxTries'] = 1;
        $options['force'] = false;
        $options['stopWhenEmpty'] = false;
        $options['once'] = true;

        return carr::get($options, $name);
    }

}
