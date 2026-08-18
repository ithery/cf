<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CModel_Tracker_TrackerRefererTrait {
    public function domain() {
        return $this->belongsTo(CTracker::config()->get('domainModel', 'CTracker_Model_Domain'));
    }
}
