<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:27:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CQueue_Event_Looping
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;
    /**
     * The queue name.
     *
     * @var string
     */
    public $queue;
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct($connectionName, $queue)
    {
        $this->queue = $queue;
        $this->connectionName = $connectionName;
    }
}