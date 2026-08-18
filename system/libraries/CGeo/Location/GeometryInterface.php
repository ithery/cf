<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CGeo_Location_GeometryInterface {
    /**
     * Returns an array containing all assigned points.
     *
     * @return array
     */
    public function getPoints();
}
