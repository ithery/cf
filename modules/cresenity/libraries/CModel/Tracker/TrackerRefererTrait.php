<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 20, 2019, 10:34:48 PM
 */
trait CModel_Tracker_TrackerRefererTrait {
    public function domain() {
        return $this->belongsTo(CTracker::config()->get('domainModel', 'CTracker_Model_Domain'));
    }
}
