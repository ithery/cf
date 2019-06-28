<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 5:08:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CTracker_GeoIp_GeoIpInterface {

    public function searchAddr($addr);
}
