<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 4:17:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CQueue_ConnectorInterface {

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return CQueue_QueueInterface
     */
    public function connect(array $config);
}
