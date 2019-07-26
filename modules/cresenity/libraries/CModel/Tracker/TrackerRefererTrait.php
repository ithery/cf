<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:34:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerRefererTrait {

    public function domain() {
        return $this->belongsTo(CTracker::config()->get('domainModel', 'CTracker_Model_Domain'));
    }

}
