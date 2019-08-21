<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:35:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerRouteTrait {

    public function paths() {
        return $this->hasMany($this->getConfig()->get('routePathModel', 'CTracker_Model_RoutePath'));
    }

}
