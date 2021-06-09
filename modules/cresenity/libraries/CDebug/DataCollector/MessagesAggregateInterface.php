<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 3:11:53 PM
 */

interface CDebug_DataCollector_MessagesAggregateInterface {
    /**
     * Returns collected messages
     *
     * @return array
     */
    public function getMessages();
}
