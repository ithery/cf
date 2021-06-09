<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:25:28 PM
 */
interface CGeo_Location_GeometryInterface {
    /**
     * Returns an array containing all assigned points.
     *
     * @return array
     */
    public function getPoints();
}
