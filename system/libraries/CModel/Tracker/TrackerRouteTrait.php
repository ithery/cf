<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CModel_Tracker_TrackerRouteTrait {
    public function paths() {
        return $this->hasMany($this->getConfig()->get('routePathModel', 'CTracker_Model_RoutePath'));
    }
}
