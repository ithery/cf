<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 6:26:50 AM
 */
class CQueue_Event_JobExceptionOccurred {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var \CQueue_JobInterface
     */
    public $job;

    /**
     * The exception instance.
     *
     * @var \Exception
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param string               $connectionName
     * @param \CQueue_JobInterface $job
     * @param \Exception           $exception
     *
     * @return void
     */
    public function __construct($connectionName, $job, $exception) {
        $this->job = $job;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }
}
