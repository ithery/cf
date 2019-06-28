<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 11:16:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Psr\Log\NullLogger;

class CTracker_Tracker {

    use CTracker_Trait_TrackableTrait;

    private $booted = false;

    /**
     * @var CRouting_Router
     */
    protected $route;

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

    /**
     *
     * @var array
     */
    protected $sessionData;

    /**
     *
     * @var CTracker_Config
     */
    protected $config;

    public function __construct() {
        $this->request = CHTTP::request();
        $this->repositoryManager = CTracker_RepositoryManager::instance();
        $this->config = CTracker_Config::instance();
        $this->route = CFRouter::routedUri(CFRouter::currentUri());
        $this->logger = $this->config->getLogger() ? $this->config->getLogger() : new NullLogger();
    }

    /**
     * @return array
     */
    protected function getLogData() {
        
        return [
            'log_session_id' => $this->getSessionId(true),
            'method' => $this->request->method(),
            'log_path_id' => $this->getPathId(),
            'log_query_id' => $this->getQueryId(),
            'log_referer_id' => $this->getRefererId(),
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
        return $this->config->isLogPath() ? $this->repositoryManager->findOrCreatePath(['path' => $this->request->path(),]) : null;
    }

    public function getQueryId() {
        if ($this->config->isLogQuery()) {
            if (count($arguments = $this->request->query())) {
                return $this->repositoryManager->getQueryId(
                                [
                                    'query' => carr::implode('=', '|', $arguments),
                                    'arguments' => $arguments,
                                ]
                );
            }
        }
    }

    public function isRobot() {
        return $this->repositoryManager->isRobot();
    }

    public function logSqlQuery($query, $bindings, $time, $name) {
        if (
                $this->isTrackable() &&
                $this->config->isLogEnabled() &&
                $this->config->isLogSqlQuery() &&
                $this->isSqlQueriesLoggableConnection($name)
        ) {
            $this->repositoryManager->logSqlQuery($query, $bindings, $time, $name);
        }
    }

    protected function isSqlQueriesLoggableConnection($name) {
        return !in_array($name, $this->config->get('excludeConnection'));
    }

    public function boot() {
        if ($this->booted) {
            return false;
        }
        $this->booted = true;
        if ($this->isTrackable()) {
            $this->track();
        }
    }

    private function getLogger() {
        return $this->logger;
    }

    public function track() {
        $log = $this->getLogData();
        if ($this->config->isLogEnabled()) {
            $this->repositoryManager->createLog($log);
        }
    }

}
