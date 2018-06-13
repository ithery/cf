<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 4:40:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CApp_Api_Method implements CApp_Api_MethodInterface {

    /**
     *
     * @var CApp_Api
     */
    protected $api;
    protected $method;
    protected $errCode = 0;
    protected $errMessage = "";
    protected $data = array();
    protected $refId;
    protected $domain;
    private $request = null;

    final public function __construct(CApp_Api $api, $method, $request = null) {
        $this->api = $api;
        $this->domain = $this->api->getDomain();
        $this->method = $method;
        $this->request = $request;
        $this->refId = md5(uniqid()) . uniqid();
    }

    public function sessionId() {
        return $this->refId;
    }

    public function request() {
        if ($this->request == null) {
            return array_merge($_GET, $_POST);
        }
        return $this->request;
    }

    public function result() {
        $return = array(
            'err_code' => $this->errCode,
            'err_message' => $this->errMessage,
            'data' => $this->data,
        );
        return $return;
    }

    public function getErrCode() {
        return $this->errCode;
    }

    public function getErrMessage() {
        return $this->errMessage;
    }

}
