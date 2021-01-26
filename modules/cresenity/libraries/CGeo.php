<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:03:04 PM
 * @deprecated 2.0
 */
class CGeo {
    public static function ip() {
        return new CGeo_IP();
    }

    public static function createCoordinate($lat, $lng) {
        return new CGeo_Location_Coordinate($lat, $lng);
    }
}
