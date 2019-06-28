<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 9:01:15 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerSessionTrait {

    public function user() {
        return $this->belongsTo('CApp_Model_Users');
    }

    public function device() {
        return $this->belongsTo('CTracker_Model_Device');
    }

    public function language() {
        return $this->belongsTo('CTracker_Model_Language');
    }

    public function agent() {
        return $this->belongsTo('CTracker_Model_Agent');
    }

    public function referer() {
        return $this->belongsTo('CTracker_Model_Referer');
    }

    public function geoIp() {
        return $this->belongsTo('CTracker_Model_GeoIp');
    }

    public function cookie() {
        return $this->belongsTo('CTracker_Model_Cookie');
    }

    public function log() {
        return $this->hasMany('CTracker_Model_Log');
    }

    public function getPageViewsAttribute() {
        return $this->log()->count();
    }

    public function users(CPeriod $minutes, $result) {
        $query = $this
                ->select(
                        'user_id', $this->getConnection()->raw('max(updated) as updated')
                )
                ->groupBy('user_id')
                ->from('log_session')
                ->period($minutes)
                ->whereNotNull('user_id')
                ->orderBy($this->getConnection()->raw('max(updated)'), 'desc');
        if ($result) {
            return $query->get();
        }
        return $query;
    }

    public function userDevices(CPeriod $minutes, $result, $user_id) {
        $query = $this
                ->select(
                        'user_id', $this->getConnection()->raw('max(updated) as updated')
                )
                ->groupBy('user_id')
                ->from('log_sessions')
                ->period($minutes)
                ->whereNotNull('user_id')
                ->orderBy($this->getConnection()->raw('max(updated)'), 'desc');
        if ($result) {
            return $query->get();
        }
        return $query;
    }

}
