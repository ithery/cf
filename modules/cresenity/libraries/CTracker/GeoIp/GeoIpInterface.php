<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 5:08:03 PM
 */
interface CTracker_GeoIp_GeoIpInterface {
    public function searchAddr($addr);
}
