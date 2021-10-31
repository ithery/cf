<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 3:50:20 AM
 */
interface CQueue_FactoryInterface {
    /**
     * Resolve a queue connection instance.
     *
     * @param string|null $name
     *
     * @return \CQueue_QueueInterface
     */
    public function connection($name = null);
}
