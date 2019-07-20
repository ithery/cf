<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:30:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerQueryTrait {
    
    public function arguments() {
        return $this->hasMany($this->getConfig()->get('queryArgumentModel', 'CTracker_Model_QueryArgument'));
    }
}
