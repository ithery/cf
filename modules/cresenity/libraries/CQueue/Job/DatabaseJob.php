<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:02:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CQueue_Job_DatabaseJob extends CQueue_AbstractJob implements CQueue_JobInterface {

    /**
     * The database queue instance.
     *
     * @var CQueue_Queue_DatabaseQueue
     */
    protected $database;

    /**
     * The database job payload.
     *
     * @var \stdClass
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  CContainer_Container  $container
     * @param  CQueue_Queue_DatabaseQueue  $database
     * @param  \stdClass  $job
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(CContainer_Container $container, CQueue_Queue_DatabaseQueue $database, $job, $connectionName, $queue) {
        $this->job = $job;
        $this->queue = $queue;
        $this->database = $database;
        $this->container = $container;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int  $delay
     * @return mixed
     */
    public function release($delay = 0) {
        parent::release($delay);
        $this->delete();
        return $this->database->release($this->queue, $this->job, $delay);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete() {
        parent::delete();
        $this->database->deleteReserved($this->queue, $this->job->queue_id);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts() {
        return (int) $this->job->attempts;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId() {
        return $this->job->queue_id;
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody() {
        return $this->job->payload;
    }

}
