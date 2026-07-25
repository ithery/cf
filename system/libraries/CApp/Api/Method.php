<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CApp_Api_Method implements CApp_Api_MethodInterface {
    /**
     * @var CApp_Api
     */
    protected $api;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var int
     */
    protected $errCode = 0;

    /**
     * @var string
     */
    protected $errMessage = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $refId;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var array|null
     */
    private $request = null;

    /**
     * @param CApp_Api   $api
     * @param string     $method
     * @param array|null $request
     */
    public function __construct(CApp_Api $api, $method, $request = null) {
        $this->api = $api;
        $this->domain = $this->api->getDomain();
        $this->method = $method;
        $this->request = $request;
        $this->refId = md5(uniqid()) . uniqid();
        if (!isset($_GET['auth'])) {
            $this->auth();
        }
    }

    /**
     * @return string
     */
    public function sessionId() {
        return $this->refId;
    }

    /**
     * @return array
     */
    public function request() {
        if ($this->request == null) {
            return array_merge($_GET, $_POST);
        }

        return $this->request;
    }

    /**
     * @return array
     */
    public function result() {
        $data = $this->data;
        if (is_array($data) && count($data) == 0) {
            $data = (object) $data;
        }
        $return = [
            'errCode' => $this->errCode,
            'errMessage' => $this->errMessage,
            'data' => $data,
        ];

        return $return;
    }

    /**
     * @return int
     */
    public function getErrCode() {
        return $this->errCode;
    }

    /**
     * @return string
     */
    public function getErrMessage() {
        return $this->errMessage;
    }

    /**
     * @return void
     */
    public function auth() {
        $apiKey = CF::config('devcloud.api_key');
        $secretKey = CF::config('devcloud.secret_key');

        $requestApiKey = carr::get($this->request(), 'apiKey');
        $requestSecretKey = carr::get($this->request(), 'secretKey');

        if (empty($apiKey)) {
            $this->errCode++;
            $this->errMessage = 'Project api_key not configured yet.';
        }

        if (empty($secretKey)) {
            $this->errCode++;
            $this->errMessage = 'Project secret_key not configured yet.';
        }

        if ($apiKey != $requestApiKey) {
            $this->errCode++;
            $this->errMessage = 'Invalid API Key';
        }

        if ($secretKey != $requestSecretKey) {
            $this->errCode++;
            $this->errMessage = 'Invalid Secret Key';
        }

        if (empty($requestApiKey)) {
            $this->errCode++;
            $this->errMessage = 'api_key is required.';
        }

        if (empty($requestSecretKey)) {
            $this->errCode++;
            $this->errMessage = 'secret_key is required.';
        }

        if ($apiKey != $requestApiKey && $secretKey != $requestSecretKey) {
            $this->errCode = 9999;
            $this->errMessage = 'Authentication Failed! ';// . json_encode(c::request()->secure()) . '#' . json_encode(c::request()->all()) . '|' . CF::domain() . '|' . $apiKey . '|' . $requestApiKey . '|' . $secretKey . '|' . $requestSecretKey;
        }
    }

    /**
     * @return string
     */
    public function domain() {
        return $this->domain;
    }
}
