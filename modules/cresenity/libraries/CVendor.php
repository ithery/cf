<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;

class CVendor {

    /**
     * 
     * @param string $accessToken
     * @return \CVendor_DigitalOcean
     */
    public static function digitalOcean($accessToken) {
        return new CVendor_DigitalOcean($accessToken);
    }

    /**
     * 
     * @param array $options
     * @return \CVendor_OneSignal
     */
    public static function oneSignal($options) {
        $appId = carr::get($options, 'app_id');
        $appKey = carr::get($options, 'app_key');
        $userKey = carr::get($options, 'user_key');
        $config = new CVendor_OneSignal_Config();
        if (strlen($appId) > 0) {
            $config->setApplicationId($appId);
        }
        if (strlen($appKey) > 0) {
            $config->setApplicationAuthKey($appKey);
        }
        if (strlen($userKey) > 0) {
            $config->setUserAuthKey($userKey);
        }



        $guzzle = new GuzzleClient([// http://docs.guzzlephp.org/en/stable/quickstart.html
                // ..config
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $api = new CVendor_OneSignal($config, $client);
        return $api;
    }

    public static function rajaOngkir() {
        return new CVendor_RajaOngkir();
    }

    public static function shipper($environment = 'production') {
        return new CVendor_Shipper($environment);
    }

    public static function senangPay($options, $environment = 'production') {
        return new CVendor_SenangPay($options, $environment);
    }

    public static function posMalaysia() {
        return new CVendor_PosMalaysia();
    }

    /**
     * 
     * @param array $environment
     * @return \CVendor_GoSend
     */
    public static function goSend($environment = 'production') {
        return new CVendor_GoSend($environment);
    }

    /**
     * 
     * @param array $options
     * @return \CVendor_Namecheap
     */
    public static function namecheap($options) {
        return new CVendor_Namecheap($options);
    }

    /**
     * 
     * @param array $options
     * @return CVendor_LetsEncrypt
     */
    public static function letsEncrypt($options) {
        return CVendor_LetsEncrypt::instance($options);
    }

    /**
     * 
     * @param type $options
     * @return \CVendor_Xendit
     */
    public static function xendit($options) {
        return new CVendor_Xendit($options);
    }

}
