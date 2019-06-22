<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:44:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Bootstrap {

    /**
     *
     * @var CHTTP_Request
     */
    protected $request;

    /**
     *
     * @var CTracker_RepositoryManager 
     */
    protected $repositoryManager;

    public function __construct() {
        $this->request = CHTTP::request();
        $this->repositoryManager = CTracker_RepositoryManager::instance();
        $this->config = CTracker_Config::instance();
    }

    public function execute() {

        $logData = $this->getLogData();
    }

    /**
     * @return array
     */
    protected function getLogData() {
        return [
            'session_id' => $this->getSessionId(true),
            'method' => $this->request->method(),
            'path_id' => $this->getPathId(),
            'query_id' => $this->getQueryId(),
            'referer_id' => $this->getRefererId(),
            'is_ajax' => $this->request->ajax(),
            'is_secure' => $this->request->isSecure(),
            'is_json' => $this->request->isJson(),
            'wants_json' => $this->request->wantsJson(),
        ];
    }

    public function getSessionId($updateLastActivity = false) {
        return $this->repositoryManager->getSessionId(
                        $this->makeSessionData(), $updateLastActivity
        );
    }

    /**
     * @return array
     */
    protected function makeSessionData() {
        $sessionData = [
            'user_id' => $this->getUserId(),
            'device_id' => $this->getDeviceId(),
            'client_ip' => $this->request->getClientIp(),
            'geoip_id' => $this->getGeoIpId(),
            'agent_id' => $this->getAgentId(),
            'referer_id' => $this->getRefererId(),
            'cookie_id' => $this->getCookieId(),
            'language_id' => $this->getLanguageId(),
            'is_robot' => $this->isRobot(),
            // The key user_agent is not present in the sessions table, but
            // it's internally used to check if the user agent changed
            // during a session.
            'user_agent' => $this->repositoryManager->getCurrentUserAgent(),
        ];
        return $this->sessionData = $this->repositoryManager->checkSessionData($sessionData, $this->sessionData);
    }

    public function getUserId() {
        return $this->repositoryManager->getCurrentUserId();
    }

    public function getDeviceId() {
        return $this->config->isLogDevice() ? $this->repositoryManager->findOrCreateDevice(
                        $this->repositoryManager->getCurrentDeviceProperties()
                ) : null;
    }

}
