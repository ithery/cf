<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:29:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Implementation of distance calculation with http://en.wikipedia.org/wiki/Law_of_haversines
 * @see      http://en.wikipedia.org/wiki/Law_of_haversines
 *
 */
class CGeo_Location_Distance_Haversine implements CGeo_Location_Distance_DistanceInterface {

    /**
     * @param Coordinate $point1
     * @param Coordinate $point2
     *
     * @throws CGeo_Location_Exception_NotMatchingEllipsoidException
     *
     * @return float
     */
    public function getDistance(CGeo_Location_Coordinate $point1, CGeo_Location_Coordinate $point2) {
        if ($point1->getEllipsoid() != $point2->getEllipsoid()) {
            throw new CGeo_Location_Exception_NotMatchingEllipsoidException('The ellipsoids for both coordinates must match');
        }
        $lat1 = deg2rad($point1->getLat());
        $lat2 = deg2rad($point2->getLat());
        $lng1 = deg2rad($point1->getLng());
        $lng2 = deg2rad($point2->getLng());
        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;
        $radius = $point1->getEllipsoid()->getArithmeticMeanRadius();
        $distance = 2 * $radius * asin(
                        sqrt(
                                (sin($dLat / 2) ** 2) + cos($lat1) * cos($lat2) * (sin($dLng / 2) ** 2)
                        )
        );
        return round($distance, 3);
    }

}
