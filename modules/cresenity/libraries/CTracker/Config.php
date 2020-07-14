<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:21:02 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CHelper_File as File;

class CTracker_Config {

    protected static $instance;
    protected $data;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_Config();
        }
        return static::$instance;
    }

    public function __construct() {
        $default = array();
        $default['sessionModel'] = CTracker_Model_Session::class;
        $default['pathModel'] = CTracker_Model_Path::class;
        $default['logModel'] = CTracker_Model_Log::class;
        $default['refererModel'] = CTracker_Model_Referer::class;
        $default['refererSearchTermModel'] = CTracker_Model_RefererSearchTerm::class;
        $default['languageModel'] = CTracker_Model_Language::class;
        $default['routeModel'] = CTracker_Model_Route::class;
        $default['routePathModel'] = CTracker_Model_RoutePath::class;
        $default['routePathParameterModel'] = CTracker_Model_RoutePathParameter::class;
        $default['pathModel'] = CTracker_Model_Path::class;
        $default['connectionModel'] = CTracker_Model_Connection::class;
        $default['geoIpModel'] = CTracker_Model_GeoIp::class;
        $default['domainModel'] = CTracker_Model_Domain::class;
        $default['deviceModel'] = CTracker_Model_Device::class;
        $default['cookieModel'] = CTracker_Model_Cookie::class;
        $default['agentModel'] = CTracker_Model_Agent::class;
        $default['errorModel'] = CTracker_Model_Error::class;
        $default['queryModel'] = CTracker_Model_Query::class;
        $default['queryArgumentModel'] = CTracker_Model_QueryArgument::class;
        $default['sqlQueryModel'] = CTracker_Model_SqlQuery::class;
        $default['sqlQueryBindingModel'] = CTracker_Model_SqlQueryBinding::class;
        $default['sqlQueryBindingParameterModel'] = CTracker_Model_SqlQueryBindingParameter::class;
        $default['sqlQueryLogModel'] = CTracker_Model_SqlQueryLog::class;
        $default['systemClassModel'] = CTracker_Model_SystemClass::class;
        $default['logCookie'] = true;
        $default['logDevice'] = true;
        $default['logGeoIp'] = true;
        $default['logReferer'] = true;
        $default['logAgent'] = true;
        $default['logQuery'] = true;
        $default['logSqlQuery'] = false;
        $default['logSqlQueryBinding'] = false;
        $default['logPath'] = true;
        $default['logLanguage'] = true;
        $default['logUntrackable'] = true;
        $default['cacheEnabled'] = false;
        $default['trackEnabled'] = true;
        $default['robotEnabled'] = false;
        $default['consoleEnabled'] = false;
        $default['isQueued'] = false;
        
        $default['queueConnection'] = 'database';
        $default['trackQueueClass'] = CTracker_TaskQueue_TrackQueue::class;
        $default['database'] = 'default';
        $default['logger'] = null;
        $default['logEnabled'] = true;
        $default['cookieNamespace'] = 'CTrackerCookie';
        $default['sessionKey'] = 'CTrackerSession';
        $default['sessionNamespace'] = 'CTracker';
        $default['sessionClass'] = 'CTracker_Session';
        $default['excludeConnection'] = [];
        $default['excludeIpAddress'] = [];
        $default['excludeEnvironment'] = [];
        $default['excludePath'] = [
            'favicon.ico',
            'cresenity/ajax/*',
            'cresenity/noimage',
            'cresenity/noimage/*',
//            'unittest/*',
            'unit_test/*',
            'test/*',
            'ccore/*',
            '*.js',
            '*.js.map',
            '*.css',
            '*.css.map',
            '*.jpg',
            '*.jpeg',
            '*.png',
        ];
        $default['excludeRoute'] = [];
        $this->data = CConfig::instance('tracker')->get();
        if (!is_array($this->data)) {
            $this->data = array();
        }
        $this->data = array_merge($default, $this->data);
    }

    public function get($key, $default = null) {
        return carr::get($this->data, $key, $default);
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function isLogDevice() {
        return $this->get('logDevice');
    }

    public function isLogGeoIp() {
        return $this->get('logGeoIp');
    }

    public function isLogAgent() {
        return $this->get('logAgent');
    }

    public function isLogReferer() {
        return $this->get('logReferer');
    }

    public function isLogCookie() {
        return $this->get('logCookie');
    }

    public function isLogLanguage() {
        return $this->get('logLanguage');
    }

    public function isLogPath() {
        return $this->get('logPath');
    }

    public function isLogQuery() {
        return $this->get('logQuery');
    }

    public function isLogSqlQuery() {
        return $this->get('logSqlQuery', true);
    }

    public function isLogSqlQueryBinding() {
        return $this->get('logSqlQueryBinding', true);
    }

    public function isCacheEnabled() {
        return $this->get('cacheEnabled', false);
    }

    public function isRobotEnabled() {
        return $this->get('robotEnabled', false);
    }

    public function isLogUntrackable() {
        return $this->get('logUntrackable', true);
    }

    public function isLogEnabled() {
        return $this->get('logEnabled', true);
    }

    public function isTrackEnabled() {
        return $this->get('trackEnabled', true);
    }

    public function isConsoleEnabled() {
        return $this->get('consoleEnabled', false);
    }

    public function getExcludeEnvironment() {
        return $this->get('excludeEnvironment', []);
    }

    public function getExcludeIpAddress() {
        return $this->get('excludeIpAddress', []);
    }

    public function getExcludeConnection() {
        return $this->get('excludeConnection', []);
    }

    public function getExcludePath() {
        return $this->get('excludePath', []);
    }

    public function getExcludeRoute() {
        return $this->get('excludeRoute', []);
    }

    public function cookieNamespace() {
        return $this->get('cookieNamespace');
    }

    public function isQueued() {
        return $this->get('isQueued');
    }

    public function getLogger() {
        $logger = $this->get('logger');
        if ($logger != null && is_string($logger)) {
            $logger = new $logger();
        }
        if ($logger != null && !($logger instanceof \Psr\Log\LoggerInterface)) {
            return null;
        }
    }

    public function isMongo() {
        $driver = CF::config('database.' . $this->get('database') . '.connection.type');
        return $driver == 'mongodb';
    }

}
