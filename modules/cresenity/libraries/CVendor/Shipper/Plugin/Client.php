<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Shipper_Plugin_Client {

    use CTrait_HasOptions;

    private $apiKey;
    private $apiType;
    private $args;
    private $baseUrl;

    public function __construct($options) {
        if (!function_exists('curl_init')) {
            die('cURL Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
        }
        $this->options = $options;
        $this->apiType = $this->getOption('apiType', 'starter');
        $this->apiKey = $this->getOption('starter');
        $this->baseUrl = "https://shipper.id/plugin/api/v1/";
    }

    public function request($request, $method = 'GET', $params = array()) {

        if ($method == 'GET') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Api-Key:{$this->apiKey}",
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);

            $err = curl_error($curl);
        } elseif ($method == 'PUT') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_HTTPHEADER => array(
                    "Api-Key:{$this->apiKey}",
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
        } else {
            $curl = curl_init();
            $params_string = '';
            $params_string = json_encode($params);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $params_string, // parameternya string kan? bukan array?
                CURLOPT_HTTPHEADER => array(
                    "Api-Key:{$this->apiKey}",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
        }

        return $response;
    }

    public function init() {
        if (isset($this->apiType)) {
            $type = $this->apiType;
        } else {
            $type = 'starter';
        }
        $response = $this->getProvince();
        if ($response->rajaongkir->status->code != 200) {
            return array('status' => false, 'message' => $response->rajaongkir->status->description);
        } else {
            return array('status' => true, 'message' => $response->rajaongkir->status->description);
        }
    }

    public function getProvince() {
        $response = $this->request($this->baseUrl . 'geodata/provs', 'GET');
        $this->dataLog($this->baseUrl . 'geodata/provs', $request = '', $response);
        return json_decode($response);
    }

    public function getCity($provinces = NULL) {
        if ($provinces) {
            $url = $this->baseUrl . "/geodata/cities?prov=" . $provinces;
            $response = $this->request($url, 'GET');
        } else {
            $url = $this->baseUrl . "/geodata/cities?origin=all";
            $response = $this->request($url, 'GET');
        }

        $this->dataLog($url, $request = '', $response);

        return json_decode($response);
    }

    public function getCheckoutCity($provinces = NULL) {
        if ($provinces) {
            $response = $this->request($this->baseUrl . "/geodata/cities?prov=" . $provinces, 'GET');
            $this->dataLog($this->baseUrl . "/geodata/cities?prov=" . $provinces, $request = '', $response);
        } else {
            $response = array();
        }
        return json_decode($response);
    }

    public function getDistrict($id_city) {
        if (!$id_city)
            return false;

        $response = $this->request($this->baseUrl . '/geodata/suburbs?city=' . $id_city, 'GET');
        $this->dataLog($this->baseUrl . '/geodata/suburbs?city=' . $id_city, $request = '', $response);

        return json_decode($response);
    }

    public function getAreas($suburbs) {
        if (!$suburbs)
            return false;

        $response = $this->request($this->baseUrl . '/geodata/areas?sub=' . $suburbs, 'GET');
        $this->dataLog($this->baseUrl . '/geodata/areas?sub=' . $suburbs, $request = '', $response);

        return json_decode($response);
    }

    public function getDetail($suburbs) {
        if (!$suburbs)
            return false;

        $response = $this->request($this->baseUrl . '/details?suburb=' . $suburbs, 'GET');
        $this->dataLog($this->baseUrl . '/details?suburb=' . $suburbs, $request = '', $response);

        return json_decode($response);
    }

    public function createOrder($arg) {

        if (!$arg)
            return false;

        $response = $this->request($this->baseUrl . '/orders/domestics', 'POST', $arg);
        $this->dataLog($this->baseUrl . '/orders/domestics', $arg, $response);
        return json_decode($response);
    }

    public function confirmOrder($arg) {
        if (!$arg)
            return false;

        $response = $this->request($this->baseUrl . '/orders/confirm/' . $arg, 'PUT', array());
        $this->dataLog($this->baseUrl . '/orders/confirm/' . $arg, $request = '', $response);
        return json_decode($response);
    }

    public function getCost($origin, $destination, $weight = NULL, $price = NULL, $length = NULL, $width = NULL, $height = NULL, $type = NULL, $cod = 1) {
        global $wpdb, $options_conf, $woocommerce;
        $weight = $weight ? $weight : 1;
        $length = $length ? $length : 30;
        $width = $width ? $width : 20;
        $height = $height ? $height : 10;
        $price = $price ? $price : 50000;
        $type = $type ? $type : 2;
        $cod = $cod ? $cod : 0;

        $url = $this->baseUrl . '/rates/domestics/search?o=' . $origin . '&d=' . $destination . '&wt=' . $weight . '&l=' . $length . '&w=' . $width . '&h=' . $height . '&v=' . $price . '&type=' . $type . '&apiKey=' . $this->apiKey;
        $response = $this->request($url, 'GET');
        $this->dataLog($url, $request = '', $response);
        return json_decode($response);
    }

    public function checkCondition($api) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://rajaongkir.com/api/starter/city",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: " . $api
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
    }

    public function activityLog($action, $arg, $step, $fullstep) {
        if (!$arg)
            return false;

        $data = array(
            'activity' => $arg,
            'action' => $action,
            'step' => $step,
            'fullstep' => $fullstep
        );

        $response = $this->request($this->baseUrl . '/log/activity', 'POST', $data);

        return json_decode($response);
    }

    public function dataLog($endpoint, $request, $response) {



        $data = array(
            'endpoint' => $endpoint,
            'request' => json_encode($request),
            'response' => json_encode($response)
        );

        if ($this->getOption('debug') == 'yes') {
            $response = json_decode($this->request($this->baseUrl . '/log/data', 'POST', $data));
            if ($response->message == 'Not access for log') {
                $this->setOption['debug'] = 'no';
                //update_option('woocommerce_wc_shipper_shipping_settings', $options_conf);
            }
        }
    }

}
