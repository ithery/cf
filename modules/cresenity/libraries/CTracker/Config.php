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
        $default['sessionModel'] = 'CTracker_Model_Session';
        $default['pathModel'] = 'CTracker_Model_Path';
        $default['logModel'] = 'CTracker_Model_Log';
        $default['refererModel'] = 'CTracker_Model_Referer';
        $default['refererSearchTermModel'] = 'CTracker_Model_RefererSearchTerm';
        $default['languageModel'] = 'CTracker_Model_Language';
        $default['geoIpModel'] = 'CTracker_Model_GeoIp';
        $default['domainModel'] = 'CTracker_Model_Domain';
        $default['deviceModel'] = 'CTracker_Model_Device';
        $default['cookieModel'] = 'CTracker_Model_Cookie';
        $default['agentModel'] = 'CTracker_Model_Agent';
        $default['errorModel'] = 'CTracker_Model_Error';
        $default['queryModel'] = 'CTracker_Model_Query';
        $default['queryArgumentModel'] = 'CTracker_Model_QueryArgument';
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
        $default['logger'] = null;
        $default['logEnabled'] = true;
        $default['cookieNamespace'] = 'CTrackerCookie';
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

    public function getLogger() {
        $logger = $this->get('logger');
        if ($logger != null && is_string($logger)) {
            $logger = new $logger();
        }
        if ($logger != null && !($logger instanceof \Psr\Log\LoggerInterface)) {
            return null;
        }
    }

}
