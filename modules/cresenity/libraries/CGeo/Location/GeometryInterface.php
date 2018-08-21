<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:25:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CGeo_Location_GeometryInterface {

    /**
     * Returns an array containing all assigned points.
     *
     * @return array
     */
    public function getPoints();
}
