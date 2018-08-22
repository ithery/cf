<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 3:11:53 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

interface CDebug_DataCollector_MessagesAggregateInterface
{
    /**
     * Returns collected messages
     *
     * @return array
     */
    public function getMessages();
}