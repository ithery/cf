<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Cloud_Api {
    /**
     * @var string
     */
    const ENDPOINT = 'https://cpanel.ittron.co.id/api';

    /**
     * @var CApp_Cloud_AdapterInterface
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $endPoint;

    /**
     * @param CApp_Cloud_AdapterInterface $adapter
     * @param null|string                 $endPoint
     */
    public function __construct(CApp_Cloud_AdapterInterface $adapter, $endPoint = null) {
        $this->adapter = $adapter;
        $this->endPoint = $endPoint ?: static::ENDPOINT;
    }

    /**
     * Build the default POST payload sent with every API request.
     *
     * @return array
     */
    public function getDefaultPost() {
        $default = [];
        $default['domain'] = CF::domain();

        return $default;
    }

    /**
     * Execute an API query, merging the given data with the default POST payload.
     *
     * @param string $query    the API endpoint path to call
     * @param array  $postData additional POST data merged with the default payload
     *
     * @throws CApp_Cloud_Exception_ApiException if the response is not a valid array or the API reports an error
     *
     * @return mixed the 'data' entry of the decoded API response
     */
    public function execute($query, $postData = []) {
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
                throw new CApp_Cloud_Exception_ApiException('Response is not array: ' . $response);
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
