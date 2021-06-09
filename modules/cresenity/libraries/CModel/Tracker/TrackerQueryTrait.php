<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 20, 2019, 10:30:54 PM
 */
trait CModel_Tracker_TrackerQueryTrait {
    public function arguments() {
        return $this->hasMany($this->getConfig()->get('queryArgumentModel', 'CTracker_Model_QueryArgument'));
    }
}
