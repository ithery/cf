<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:58:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerRoutePathTrait {

    public function parameters() {
        return $this->hasMany(CTracker::config()->get('routePathParameterModel', 'CTracker_Model_RoutePathParameter'));
    }

    public function route() {
        return $this->belongsTo(CTracker::config()->get('routeModel', 'CTracker_Model_Route'), 'log_route_id');
    }

}
