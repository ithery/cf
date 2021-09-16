<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 9:32:14 PM
 */
abstract class CApp_Remote_Client_Engine {
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $baseApiUrl;

    public function __construct($options) {
        $this->domain = carr::get($options, 'domain');
        $this->options = $options;
        $protocol = carr::get($options, 'protocol', 'http');
        $port = carr::get($options, 'port', 80);
        $this->baseUrl = $protocol . '://' . $this->domain . ':' . $port . '/cresenity/api/';
    }

    public function request($url, $post = []) {
        $curl = CCurl::factory($url);
        $curl->setPost($post);
        $response = $curl->exec()->response();
        $responseData = json_decode($response, true);
        if (!is_array($responseData)) {
            //failed to decode json
            throw new CException('failed to decode api from url :url, response :response', [':url' => $url, ':response' => htmlspecialchars($response)]);
        }

        $errCode = carr::get($responseData, 'err_code');
        $errMessage = carr::get($responseData, 'err_message');
        if ($errCode > 0) {
            throw new CApp_Exception_RemoteRequestException($errMessage, null, $errCode);
        }
        $data = carr::get($responseData, 'data');
        return $data;
    }
}
