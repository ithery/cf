<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_FactoryInterface {
    /**
     * Resolve a queue connection instance.
     *
     * @param null|string $name
     *
     * @return \CQueue_QueueInterface
     */
    public function connection($name = null);
}
