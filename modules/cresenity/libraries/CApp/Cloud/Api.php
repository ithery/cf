<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:42:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Cloud_Api {

    /**
     * @var string
     */
    const ENDPOINT = 'https://cpanel.ittron.co.id/api';

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var AdapterInterface
     */
    protected $endPoint;

    /**
     * @param CApp_Cloud_AdapterInterface $adapter
     * @param string|null      $endpoint
     */
    public function __construct(CApp_Cloud_AdapterInterface $adapter, $endPoint = null) {
        $this->adapter = $adapter;
        $this->endPoint = $endPoint ?: static::ENDPOINT;
    }

    public function getDefaultPost() {
        $default = array();
        $default['domain'] = CF::domain();
        return $default;
    }

    public function execute($query, $postData = array()) {

        $post = array_merge($this->getDefaultPost(), $postData);
        $errCode = 0;
        $errMessage = '';
        $response = '';
        try {
            $response = $this->adapter->post(sprintf('%s/%s', $this->endPoint, $query), $post);
        } catch (CApp_Cloud_Exception_HttpException $ex) {
            $errCode++;
            $errMessage = 'HTTP error with message:' . $ex->getMessage() . ', status code:' . $ex->getCode();
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = '[FATAL ERROR] ' . $ex->getMessage();
        }
        if ($errCode == 0) {
            $result = json_decode($response, true);
            if (!is_array($result)) {
                throw new CApp_Cloud_Exception_ApiException('Response is not array:' . $response);
            }
            $errCode = carr::get($result, 'errCode');
            $errMessage = carr::get($result, 'errMessage');
        }
        if ($errCode > 0) {
            throw new CApp_Cloud_Exception_ApiException($errMessage);
        }


        return carr::get($result, 'data');
    }

}
