<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CGeo_Location_Distance_DistanceInterface {
    /**
     * @param CGeo_Location_Coordinate $point1
     * @param CGeo_Location_Coordinate $point2
     *
     * @return float distance between the two coordinates in meters
     */
    public function getDistance(CGeo_Location_Coordinate $point1, CGeo_Location_Coordinate $point2);
}
