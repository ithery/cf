<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Ramsey\Uuid\Uuid as UUID;

class CTracker_Populator {

    /**
     *
     * @var CTracker_Populator
     */
    protected static $instance;

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var bool
     */
    protected $isDataPopulated;

    /**
     *
     * @var CTracker_Parser_UserAgentParser
     */
    protected $userAgentParser;

    /**
     *
     * @var CTracker_Detect_MobileDetect
     */
    protected $mobileDetect;

    private function __construct() {
        $this->data = [];
        $this->userAgentParser = new CTracker_Parser_UserAgentParser(DOCROOT);
        $this->mobileDetect = new CTracker_Detect_MobileDetect();
        $this->isDataPopulated = false;
    }

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_Populator();
        }
        return static::$instance;
    }

    public function populateSessionData() {

        $sessionData = [];
        $sessionPopulator = new CTracker_Populator_Session();
        $sessionData = $sessionPopulator->populateSessionData($this->data);


        $this->data['session'] = $sessionData;
    }

    public function populateUserData() {
        $userData = [];
        $userData['userId'] = CApp_Base::userId();
        $this->data['user'] = $userData;
    }

    public function populateData() {
        if (!$this->isDataPopulated) {
            $this->populateUserData();
            $this->populateRequestData();
            $this->populateDeviceData();
            $this->populateGeoIpData();
            $this->populateCookieData();
            $this->populateLanguageData();
            $this->populateAgentData();
            //last call to generate session data
            $this->populateSessionData();

            $this->isDataPopulated = true;
        }
        return $this;
    }

    public function setCustomSessionData(array $sessionData) {
        $this->data['customSessionData'] = $sessionData;
    }

    public function setCustomLogData(array $logData) {

        $this->data['customLogData'] = $logData;
    }

    protected function populateRequestData() {
        $request = CHTTP::request();
        $requestData = [];
        $clientIp = null;
        $userAgent = null;
        $headers = $request->header();
        if (isset($headers['x-forwarded-for'])) {
            $clientIp = carr::get($headers, 'x-forwarded-for.0');
        }
        if (strpos($clientIp, ",") !== false) {
            $clientIp = trim(carr::get(explode(",", $clientIp), 0));
        }


        if (isset($headers['user-agent'])) {
            $userAgent = carr::get($headers, 'user-agent.0');
        }

        if ($clientIp == null) {
            $clientIp = $request->getClientIp();
        }

        if ($userAgent == null) {
            $userAgent = $this->mobileDetect->getUserAgent();
        }




        $requestData['clientIp'] = $clientIp;
        $requestData['referer'] = $request->headers->get('referer');
        $requestData['method'] = $request->method();
        $requestData['path'] = $request->path();
        $requestData['query'] = $request->query();
        $requestData['isAjax'] = $request->ajax();
        $requestData['isSecure'] = $request->isSecure();
        $requestData['isJson'] = $request->isJson();
        $requestData['wantsJson'] = $request->wantsJson();
        $requestData['userAgent'] = $userAgent;
        $requestData['headers'] = CHTTP::request()->headers->all();

        $requestData['route'] = CFRouter::routedUri(CFRouter::currentUri());


        $this->data['request'] = $requestData;
    }

    protected function populateDeviceData() {


        if ($this->data['device'] = $this->mobileDetect->detectDevice()) {
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

            setcookie($config->cookieNamespace(), $cookieUuid,time() + (86400 * 30 * 12));

            //$cookieJar->queue($config->cookieNamespace(), $cookieUuid, 0);

            /**
             * directly send cookie, TODO send queued cookies when try to render response
             */
            /*
            foreach ($cookieJar->getQueuedCookies() as $cookieItem) {
                $cookieJar->
                $cookiesString = 'Set-Cookie: ' . $cookieItem . "\r\n";
                header($cookiesString);
            }
            */
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
