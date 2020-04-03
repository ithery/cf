<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:39:20 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Ramsey\Uuid\Uuid as UUID;

class CTracker_Repository_Session extends CTracker_AbstractRepository {

    private $config;
    private $session;
    private $sessionInfo;
    protected $relations = ['device', 'user', 'log', 'language', 'agent', 'referer', 'geoIp', 'cookie'];

    public function __construct() {
        $this->config = CTracker::config();
        $this->className = CTracker::config()->get('sessionModel', 'CTracker_Model_Session');
        $this->createModel();
        $sessionClass = CTracker::config()->get('sessionClass', 'CTracker_Session');
        if ($sessionClass) {
            $this->session = new $sessionClass();
        }

        parent::__construct();
    }

    public function findByUuid($uuid) {
        list($model, $cacheKey) = $this->cache->findCached($uuid, 'uuid', 'CTracker_Model_Session');
        if (!$model) {
            $model = $this->newQuery()->where('uuid', $uuid)->with($this->relations)->first();
            $this->cache->cachePut($cacheKey, $model);
        }
        return $model;
    }

    public function getCurrentId($sessionInfo) {
        $this->setSessionData($sessionInfo);
        return $this->sessionGetId();
    }

    public function setSessionData($sessionInfo) {
        $this->sessionInfo = $sessionInfo;
        $logSessionId = $this->findOrCreate($this->sessionInfo, ['uuid']);
        if ($this->model) {
            $this->updateSessionData($this->sessionInfo);
            $this->sessionInfo[$this->model->getKeyName()] = $logSessionId;
        }
    }

    private function sessionGetId() {
        if ($this->model) {
            return carr::get($this->sessionInfo, $this->model->getKeyName());
        }
        return null;
    }

    private function getSessions() {
        return $this
                        ->newQuery()
                        ->with($this->relations)
                        ->orderBy('updated', 'desc');
    }

    public function all() {
        return $this->getSessions()->get();
    }

    public function last($minutes, $returnResults) {
        $query = $this
                ->getSessions()
                ->period($minutes);
        if ($returnResults) {
            $cacheKey = 'last-sessions';
            $result = $this->cache->findCachedWithKey($cacheKey);
            if (!$result) {
                $result = $query->get();
                $this->cache->cachePut($cacheKey, $result, 1); // cache only for 1 minute
                return $result;
            }
            return $result;
        }
        return $query;
    }

    public function userDevices($minutes, $user_id, $results) {
        if (!$user_id) {
            return [];
        }
        $sessions = $this
                ->getSessions()
                ->period($minutes)
                ->where('user_id', $user_id);
        if ($results) {
            $sessions = $sessions->get()->pluck('device')->unique();
        }
        return $sessions;
    }

    public function users($minutes, $results) {
        return $this->getModel()->users($minutes, $results);
    }

    public function getCurrent() {
        return $this->getModel();
    }

    public function updateSessionData($data) {
        $session = $this->model;
        if ($session) {

            foreach ($session->getAttributes() as $name => $value) {
                if (isset($data[$name]) && $name !== 'log_session_id' && $name !== 'uuid') {
                    if (in_array($name, $session->getFillable())) {
                        $session->{$name} = carr::get($data, $name);
                    }
                }
            }
            $session->updated = CTracker::populator()->get('session.updated');
            $session->save();
        }
        return $data;
    }

}
