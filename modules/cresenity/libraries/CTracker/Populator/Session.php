<?php

use Ramsey\Uuid\Uuid as UUID;

class CTracker_Populator_Session {
    private $session;

    /**
     * @var array
     */
    private $populatorData;

    public function __construct() {
        $sessionClass = CTracker::config()->get('sessionClass', CTracker_Session::class);
        if ($sessionClass) {
            $this->session = new $sessionClass();
        }
    }

    /**
     * @param array $populatorData
     */
    public function populateSessionData($populatorData) {
        $this->populatorData = $populatorData;

        return $this->generateSessionData();
    }

    private function generateSessionData() {
        if ($this->session == null) {
            return null;
        }
        $data = $this->getSessionData();

        if (!$data || !$this->sessionIsReliable($data)) {
            $data = $this->buildSessionData();
        }
        $data = $this->ensureDataCompleted($data);

        $data['updated'] = date('Y-m-d H:i:s');
        $data['updatedTimestamp'] = time();
        $data['updatedTimezone'] = date_default_timezone_get();

        $data['activeSecond'] = carr::get($data, 'updatedTimestamp') - carr::get($data, 'createdTimestamp');
        $this->putSessionData($data);

        return $data;
    }

    private function buildSessionData() {
        $sessionData = [];
        $sessionData['userId'] = $this->populatorUserId();
        $sessionData['clientIp'] = $this->populatorClientIp();
        $sessionData['userAgent'] = $this->populatorUserAgent();
        $sessionData['created'] = date('Y-m-d H:i:s');
        $sessionData['createdTimestamp'] = time();
        $sessionData['createdTimezone'] = date_default_timezone_get();
        $sessionData['uuid'] = (string) UUID::uuid4();

        return $sessionData;
    }

    private function ensureDataCompleted($sessionData) {
        if (!isset($sessionData['created']) || !isset($sessionData['createdTimestamp']) || !isset($sessionData['createdTimezone'])) {
            $sessionData['created'] = date('Y-m-d H:i:s');
            $sessionData['createdTimestamp'] = time();
            $sessionData['createdTimezone'] = date_default_timezone_get();
        }
        if (!isset($sessionData['clientIp'])) {
            $this->populatorClientIp();
        }
        if (!isset($sessionData['userAgent'])) {
            $this->populatorUserAgent();
        }
        if (!isset($sessionData['userId'])) {
            $this->populatorUserId();
        }

        return $sessionData;
    }

    private function sessionIsReliable($data) {
        if (isset($data['userId'])) {
            if ($data['userId'] !== $this->populatorUserId()) {
                return false;
            }
        }
        if (isset($data['clientIp'])) {
            if ($data['clientIp'] !== $this->populatorClientIp()) {
                return false;
            }
        }
        if (isset($data['userAgent'])) {
            if ($data['userAgent'] !== $this->populatorUserAgent()) {
                return false;
            }
        }
        if (!isset($data['created'])) {
            return false;
        }
        $sessionSecond = CTracker::config()->get('sessionSecond');
        if ($sessionSecond && isset($data['updatedTimestamp'])) {
            if (time() - $data['updatedTimestamp'] > $sessionSecond) {
                return false;
            }
        }

        return true;
    }

    private function populatorUserId() {
        if ($this->populatorData) {
            $userId = carr::get($this->populatorData, 'customSessionData.user_id');
            if ($userId == null) {
                $userId = carr::get($this->populatorData, 'user.userId');
            }

            return $userId;
        }

        return null;
    }

    private function populatorClientIp() {
        if ($this->populatorData) {
            $userId = carr::get($this->populatorData, 'request.clientIp');

            return $userId;
        }

        return null;
    }

    private function populatorUserAgent() {
        if ($this->populatorData) {
            $userId = carr::get($this->populatorData, 'request.userAgent');
        }

        return null;
    }

    private function getSessionKey() {
        return CTracker::config()->get('sessionKey', 'CTrackerSession');
    }

    private function getSessionData() {
        if ($this->session == null) {
            return;
        }

        return $this->session->get($this->getSessionKey());
    }

    private function putSessionData($data) {
        if ($this->session == null) {
            return;
        }

        return $this->session->put($this->getSessionKey(), $data);
    }
}
