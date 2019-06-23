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
    protected $sessionData;

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
            'log_user_id' => $this->getUserId(),
            'log_device_id' => $this->getDeviceId(),
            'client_ip' => $this->request->getClientIp(),
            'log_geoip_id' => $this->getGeoIpId(),
            'log_agent_id' => $this->getAgentId(),
            'log_referer_id' => $this->getRefererId(),
            'log_cookie_id' => $this->getCookieId(),
            'log_language_id' => $this->getLanguageId(),
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

    protected function getGeoIpId() {
        return $this->config->isLogGeoIp() ? $this->repositoryManager->getGeoIpId($this->request->getClientIp()) : null;
    }

    protected function getAgentId() {
        return $this->config->isLogAgent() ? $this->repositoryManager->getAgentId() : null;
    }

    protected function getRefererId() {
        return $this->config->isLogReferer() ? $this->repositoryManager->getRefererId(
                        $this->request->headers->get('referer')
                ) : null;
    }

    public function getCookieId() {
        return $this->config->isLogCookie() ? $this->repositoryManager->getCookieId() : null;
    }

    public function getLanguageId() {
        return $this->config->isLoglanguage() ? $this->repositoryManager->findOrCreateLanguage($this->repositoryManager->getCurrentLanguage()) : null;
    }

    public function getPathId() {
        return $this->config->isLogPath() ? $this->repositoryManager->findOrCreatePath(
                        [
                            'path' => $this->request->path(),
                        ]
                ) : null;
    }

    public function getQueryId() {
        if ($this->config->isLogQuery()) {
            if (count($arguments = $this->request->query())) {
                return $this->repositoryManager->getQueryId(
                                [
                                    'query' => array_implode('=', '|', $arguments),
                                    'arguments' => $arguments,
                                ]
                );
            }
        }
    }

    public function isRobot() {
        return $this->repositoryManager->isRobot();
    }

}
