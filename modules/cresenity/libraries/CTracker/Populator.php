<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Ramsey\Uuid\Uuid as UUID;

class CTracker_Populator {

    protected static $instance;
    protected $data;
    protected $isDataPopulated;
    protected $userAgentParser;

    private function __construct() {
        $this->data = [];
        $this->userAgentParser = new CTracker_Parser_UserAgentParser(DOCROOT);
        $this->isDataPopulated = false;
    }

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_Populator();
        }
        return static::$instance;
    }

    public function populateSessionData() {
        $sessionClass = CTracker::config()->get('sessionClass', 'CTracker_Session');
        ;
        $session = new $sessionClass();
        $config = CTracker::config();
        $this->data['session'] = carr::get($session->getNamespaceData(), $config->get('sessionKey', 'CTrackerSession'));
    }

    public function populateData() {
        if (!$this->isDataPopulated) {
            $this->populateSessionData();
            $this->populateRequestData();
            $this->populateDeviceData();
            $this->populateGeoIpData();
            $this->populateCookieData();
            $this->populateLanguageData();
            $this->populateAgentData();

            $this->isDataPopulated = true;
        }
    }

    public function setCustomSessionData(array $sessionData) {
        $this->data['customSessionData'] = $sessionData;
    }

    public function setCustomLogData(array $logData) {

        $this->data['customLogData'] = $logData;
    }

    protected function populateRequestData() {
        $requestData = [];
        $request = CHTTP::request();
        $requestData['clientIp'] = $request->getClientIp();
        $requestData['referer'] = $request->headers->get('referer');
        $requestData['method'] = $request->method();
        $requestData['path'] = $request->path();
        $requestData['query'] = $request->query();
        $requestData['isAjax'] = $request->ajax();
        $requestData['isSecure'] = $request->isSecure();
        $requestData['isJson'] = $request->isJson();
        $requestData['wantsJson'] = $request->wantsJson();


        $requestData['route'] = CFRouter::routedUri(CFRouter::currentUri());


        $this->data['request'] = $requestData;
    }

    protected function populateDeviceData() {

        $mobileDetect = new CTracker_Detect_MobileDetect();
        if ($this->data['device'] = $mobileDetect->detectDevice()) {
            $operatingSystemFamily = null;
            $operatingSystemVersion = null;
            try {
                $operatingSystemFamily = $this->userAgentParser->operatingSystem->family;
            } catch (\Exception $e) {
                //do nothing
            }
            try {
                $operatingSystemVersion = $this->userAgentParser->getOperatingSystemVersion();
            } catch (\Exception $e) {
                //do nothing
            }
            $this->data['device']['platform'] = $operatingSystemFamily;
            $this->data['device']['platform_version'] = $operatingSystemVersion;
        }
    }

    protected function populateGeoIpData() {

        $this->data['clientIp'] = CHTTP::request()->getClientIp();
    }

    protected function populateCookieData() {

        $config = CTracker::config();
        $request = CHTTP::request();
        $cookieJar = CCookie::jar();
        if (!$config->isLogCookie()) {
            return;
        }
        if (!$cookieUuid = $request->cookie($config->cookieNamespace())) {

            $cookieUuid = (string) UUID::uuid4();

            $cookieJar->queue($config->cookieNamespace(), $cookieUuid, 0);

            /**
             * directly send cookie, TODO send queued cookies when try to render response
             */
            foreach ($cookieJar->getQueuedCookies() as $cookieItem) {
                $cookiesString = 'Set-Cookie: ' . $cookieItem . "\r\n";
                header($cookiesString);
            }
        }
        $this->data['cookie'] = [];
        $this->data['cookie']['uuid'] = $cookieUuid;
    }

    protected function populateLanguageData() {


        $languageDetect = new CTracker_Detect_LanguageDetect();

        $languages = null;
        try {
            $languages = $languageDetect->detectLanguage();
            if ($languages) {
                $languages['preference'] = $languageDetect->getLanguagePreference();
                $languages['language_range'] = $languageDetect->getLanguageRange();
            }
        } catch (Exception $ex) {
            //do noting
        }
        $this->data['language'] = $languages;
    }

    protected function populateAgentData() {
        $userAgentParser = new CTracker_Parser_UserAgentParser(DOCROOT);

        $userAgentData = [
            'name' => $name = $this->userAgentParser->originalUserAgent ?: 'Other',
            'browser' => $this->userAgentParser->userAgent->family,
            'browser_version' => $this->userAgentParser->getUserAgentVersion(),
            'name_hash' => hash('sha256', $name),
        ];
        $this->data['agent'] = $userAgentData;
    }

    public function get($key, $defaultValue = null) {
        return carr::get($this->data, $key, $defaultValue);
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

}
