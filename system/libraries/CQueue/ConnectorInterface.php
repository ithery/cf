<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_ConnectorInterface {
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return CQueue_QueueInterface
     */
    public function connect(array $config);
}
