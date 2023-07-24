<?php

defined('SYSPATH') or die('No direct access allowed.');

class CGeo {
    public static function ip() {
        return new CGeo_IP();
    }

    public static function createCoordinate($lat, $lng) {
        return new CGeo_Location_Coordinate($lat, $lng);
    }

    /**
     * @return CGeo_Geocoder
     */
    public static function geocoder() {
        return CGeo_Geocoder::instance();
    }

    /**
     * @return CGeo_Spatial
     */
    public static function spatial() {
        //TODO: move CModel_Spatial_Geometry to here
    }
}
