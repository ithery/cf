<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 20, 2019, 10:35:41 PM
 */
trait CModel_Tracker_TrackerRouteTrait {
    public function paths() {
        return $this->hasMany($this->getConfig()->get('routePathModel', 'CTracker_Model_RoutePath'));
    }
}
