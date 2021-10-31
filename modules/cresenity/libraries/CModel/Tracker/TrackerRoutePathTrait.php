<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 20, 2019, 10:58:34 PM
 */
trait CModel_Tracker_TrackerRoutePathTrait {
    public function parameters() {
        return $this->hasMany(CTracker::config()->get('routePathParameterModel', 'CTracker_Model_RoutePathParameter'));
    }

    public function route() {
        return $this->belongsTo(CTracker::config()->get('routeModel', 'CTracker_Model_Route'), 'log_route_id');
    }
}
