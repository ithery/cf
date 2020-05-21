<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:27:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class WorkerStopping {

    /**
     * The exit status.
     *
     * @var int
     */
    public $status;

    /**
     * Create a new event instance.
     *
     * @param  int  $status
     * @return void
     */
    public function __construct($status = 0) {
        $this->status = $status;
    }

}
