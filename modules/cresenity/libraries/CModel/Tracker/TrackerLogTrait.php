<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:26:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerLogTrait {

    public function session() {
        return $this->belongsTo(CTracker::config()->get('sessionModel', 'CTracker_Model_Session'));
    }

    public function path() {
        return $this->belongsTo(CTracker::config()->get('pathModel', 'CTracker_Model_Path'));
    }

    public function error() {
        return $this->belongsTo(CTracker::config()->get('errorModel', 'CTracker_Model_Error'));
    }

    public function logQuery() {
        return $this->belongsTo(CTracker::config()->get('queryModel', 'CTracker_Model_Query'));
    }

    public function routePath() {
        return $this->belongsTo(CTracker::config()->get('routePathModel', 'CTracker_Model_RoutePath'));
    }

    public function pageViews(CPeriod $minutes, $results) {
        $query = $this->select(
                        $this->getConnection()->raw('DATE(created) as date, count(*) as total')
                )->groupBy(
                        $this->getConnection()->raw('DATE(created)')
                )
                ->period($minutes)
                ->orderBy('date');
        if ($results) {
            return $query->get();
        }
        return $query;
    }

    public function pageViewsByCountry(CPeriod $minutes, $results) {
        $query = $this
                ->select(
                        'log_geoip.country_name as label', $this->getConnection()->raw('count(log_log.log_log_id) as value')
                )
                ->join('log_session', 'log_log.log_session_id', '=', 'log_session.log_session_id')
                ->join('log_geoip', 'log_session.log_geoip_id', '=', 'log_geoip.log_geoip_id')
                ->groupBy('log_geoip.country_name')
                ->period($minutes, 'log_log')
                ->whereNotNull('log_session.log_geoip_id')
                ->orderBy('value', 'desc');
        if ($results) {
            return $query->get();
        }
        return $query;
    }

    public function errors($minutes, $results) {
        $query = $this
                ->with('error')
                ->with('session')
                ->with('path')
                ->period($minutes, 'log_log')
                ->whereNotNull('log_error_id')
                ->orderBy('created', 'desc');
        if ($results) {
            return $query->get();
        }
        return $query;
    }

    public function allByRouteName($name, $minutes = null) {
        $result = $this
                ->join('tracker_route_paths', 'tracker_route_paths.id', '=', 'tracker_log.route_path_id')
                ->leftJoin(
                        'log_route_path_parameter', 'log_route_path_parameter.route_path_id', '=', 'log_route_path.id'
                )
                ->join('log_route', 'tracker_routes.id', '=', 'tracker_route_path.route_id')
                ->where('log_route.name', $name);
        if ($minutes) {
            $result->period($minutes, 'log_log');
        }
        return $result;
    }

}
