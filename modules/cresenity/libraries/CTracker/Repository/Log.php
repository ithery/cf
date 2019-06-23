<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:28:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Log extends CTracker_AbstractRepository {

    private $currentLogId;
    private $routePathId;

    public function __construct() {
        $this->className = CTracker::config()->get('logModel', 'CTracker_Model_Log');
        $this->createModel();

        parent::__construct();
    }

    public function updateRoute($routePathId = null) {
        if ($routePathId) {
            $this->routePathId = $routePathId;
        }
        $model = $this->getModel();
        if ($model->id && $this->routePathId && !$model->route_path_id) {
            $model->route_path_id = $this->routePathId;
            $model->save();
        }
        return $model;
    }

    public function updateError($error_id) {
        $model = $this->getModel();
        if ($model->id) {
            $model->log_error_id = $error_id;
            $model->save();
        }
        return $model;
    }

    public function bySession($sessionId, $results = true) {
        $query = $this
                        ->getModel()
                        ->where('session_id', $sessionId)->orderBy('updated', 'desc');
        if ($results) {
            return $query->get();
        }
        return $query;
    }

    /**
     * @return null
     */
    public function getCurrentLogId() {
        return $this->currentLogId;
    }

    /**
     * @param null|$currentLogId
     *
     * @return null|int
     */
    public function setCurrentLogId($currentLogId) {
        return $this->currentLogId = $currentLogId;
    }

    public function createLog($data) {
        $log = $this->create($data);
        $this->updateRoute();
        return $this->setCurrentLogId($log->log_log_id);
    }

    public function pageViews(CPeriod $minutes, $results) {
        return $this->getModel()->pageViews($minutes, $results);
    }

    public function pageViewsByCountry(CPeriod $minutes, $results) {
        return $this->getModel()->pageViewsByCountry($minutes, $results);
    }

    public function getErrors($minutes, $results) {
        return $this->getModel()->errors($minutes, $results);
    }

    public function allByRouteName($name, $minutes = null) {
        return $this->getModel()->allByRouteName($name, $minutes);
    }

    public function delete() {
        $this->currentLogId = null;
        $this->getModel()->delete();
    }

}
