<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Ramsey\Uuid\Uuid as UUID;

class CTracker_Populator_Session {

    private $session;
    private $populatorData;

    public function __construct() {
        $sessionClass = CTracker::config()->get('sessionClass', 'CTracker_Session');
        if ($sessionClass) {
            $this->session = new $sessionClass();
        }
            
    }

    public function populateSessionData($populatorData) {
        $this->populatorData = $populatorData;
        return $this->getSessionData();
    }

    /**
     * @param string $variable
     */
    private function getSessionData() {
        if ($this->session == null) {
            return null;
        }
        $data = $this->session->get($this->getSessionKey());

        if (!$data || !$this->sessionIsReliable($data)) {
            $data = $this->buildSessionData();
            $data['uuid'] = (string) UUID::uuid4();
        }
        $data['updated'] = date('Y-m-d H:i:s');
        $data['lastTimestamp'] = time();
        $data['lastTimezone'] = date_default_timezone_get();
        $this->putSessionData($data);
        return $data;
    }

    private function buildSessionData() {

        $sessionData = [];
        $sessionData['userId'] = $this->populatorUserId();
        $sessionData['clientIp'] = $this->populatorClientIp();
        $sessionData['userAgent'] = $this->populatorUserAgent();

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

    private function putSessionData($data) {
        if ($this->session == null) {
            return;
        }
       
        $this->session->put($this->getSessionKey(), $data);
    }

}
