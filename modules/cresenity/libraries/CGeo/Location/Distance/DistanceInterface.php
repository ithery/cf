<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:27:08 PM
 */
interface CGeo_Location_Distance_DistanceInterface {
    /**
     * @param CGeo_Location_Coordinate $point1
     * @param CGeo_Location_Coordinate $point2
     *
     * @return float distance between the two coordinates in meters
     */
    public function getDistance(CGeo_Location_Coordinate $point1, CGeo_Location_Coordinate $point2);
}
