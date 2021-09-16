<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 4:17:21 AM
 */
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
