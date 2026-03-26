<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Event_WorkerStopping {
    /**
     * The exit status.
     *
     * @var int
     */
    public $status;

    /**
     * Create a new event instance.
     *
     * @param int $status
     *
     * @return void
     */
    public function __construct($status = 0) {
        $this->status = $status;
    }
}
