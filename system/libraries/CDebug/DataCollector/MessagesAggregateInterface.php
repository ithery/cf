<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CDebug_DataCollector_MessagesAggregateInterface {
    /**
     * Returns collected messages.
     *
     * @return array
     */
    public function getMessages();
}
