<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CModel_Tracker_TrackerQueryTrait {
    public function arguments() {
        return $this->hasMany($this->getConfig()->get('queryArgumentModel', 'CTracker_Model_QueryArgument'));
    }
}
