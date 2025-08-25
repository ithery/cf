<?php

class CVendor {
    /**
     * @param string $accessToken
     *
     * @return \CVendor_DigitalOcean
     */
    public static function digitalOcean($accessToken = null) {
        if ($accessToken == null) {
            $accessToken = CF::config('vendor.digitalOcean.accessToken');
        }

        return new CVendor_DigitalOcean($accessToken);
    }

    /**
     * @param array $options
     *
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

        $api = new CVendor_OneSignal($config);

        return $api;
    }

    public static function rajaOngkir($type = 'Pro') {
        switch (strtolower($type)) {
            case 'starter':
                return new CVendor_RajaOngkir_Starter();

                break;
            case 'basic':
                return new CVendor_RajaOngkir_Basic();

                break;
            default:
                return new CVendor_RajaOngkir_Pro();

                break;
        }
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
     * @param array $environment
     *
     * @return \CVendor_GoSend
     */
    public static function goSend($environment = 'production') {
        return new CVendor_GoSend($environment);
    }

    /**
     * @return \CVendor_Namecheap
     */
    public static function namecheap() {
        return new CVendor_Namecheap();
    }

    /**
     * @param array $options
     *
     * @return CVendor_LetsEncrypt
     */
    public static function letsEncrypt($options) {
        return CVendor_LetsEncrypt::instance($options);
    }

    /**
     * @param array $options
     *
     * @return \CVendor_Xendit
     */
    public static function xendit($options) {
        return new CVendor_Xendit($options);
    }

    /**
     * @param array $options
     *
     * @return \CVendor_OneBrick
     */
    public static function oneBrick($options = []) {
        return new CVendor_OneBrick($options);
    }

    /**
     * @param array $options
     *
     * @return \CVendor_Midtrans
     */
    public static function midtrans($options) {
        return new CVendor_Midtrans($options);
    }

    /**
     * @param type $options
     *
     * @return \CVendor_Odoo_Client
     */
    public static function odoo($options) {
        return CVendor_Odoo::getClient($options);
    }

    /**
     * [zenziva description].
     *
     * @param string $username [description]
     * @param string $password [description]
     *
     * @method zenziva
     *
     * @return CVendor_Zenziva [description]
     */
    public static function zenziva($username, $password) {
        return new CVendor_Zenziva($username, $password);
    }

    /**
     * [kredivo description].
     *
     * @param string $serverKey   [<description>]
     * @param string $environment [<description>]
     *
     * @method kredivo
     *
     * @return CVendor_Kredivo [description]
     */
    public static function kredivo($serverKey, $environment = 'production') {
        return new CVendor_Kredivo($environment, $serverKey);
    }

    /**
     * @param string $apiKey
     * @param array  $options
     *
     * @return \CVendor_SendGrid
     */
    public static function sendGrid($apiKey = null, $options = []) {
        if ($apiKey == null) {
            $apiKey = CF::config('vendor.sendgrid.apiKey');
        }
        if ($apiKey == null) {
            $apiKey = CF::config('app.smtp_password');
        }

        return new CVendor_SendGrid($apiKey, $options);
    }

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param array  $options
     *
     * @return \CVendor_Nexmo
     */
    public static function nexmo($apiKey, $apiSecret, $options = []) {
        if (strlen($apiKey) == 0) {
            $apiKey = CF::config('vendor.nexmo.key');
        }
        if (strlen($apiSecret) == 0) {
            $apiSecret = CF::config('vendor.nexmo.secret');
        }
        if (!is_array($options)) {
            $options = [];
        }
        if (!isset($options['from'])) {
            $options['from'] = CF::config('vendor.nexmo.from');
        }

        return new CVendor_Nexmo($apiKey, $apiSecret, $options);
    }

    /**
     * @param array $options
     *
     * @return \CVendor_Twilio
     */
    public static function twilio($options = null) {
        if (!is_array($options)) {
            $options = CF::config('vendor.twilio');
        }

        $sid = carr::get($options, 'sid');
        $token = carr::get($options, 'token');

        return new CVendor_Twilio($sid, $token);
    }

    /**
     * @param array $options
     *
     * @return \Kreait\Firebase\Factory
     */
    public static function firebase($options = null) {
        if (!is_array($options)) {
            $options = CF::config('vendor.firebase');
        }

        return CVendor_Firebase::create($options);
    }

    /**
     * @return \CVendor_Wago|CBase_ForwarderStaticClass
     */
    public static function wago() {
        return new CBase_ForwarderStaticClass(CVendor_Wago::class);
    }

    /**
     * @return \CVendor_Watzap|CBase_ForwarderStaticClass
     */
    public static function watzap() {
        return new CBase_ForwarderStaticClass(CVendor_Watzap::class);
    }

    /**
     * @return \CVendor_TugasHarian
     */
    public static function tugasHarian() {
        return new CBase_ForwarderStaticClass(CVendor_TugasHarian::class);
    }

    /**
     * @return \CVendor_BCA
     */
    public static function bca() {
        return new CBase_ForwarderStaticClass(CVendor_BCA::class);
    }

    /**
     * @return \CVendor_WhatsApp|CBase_ForwarderStaticClass
     */
    public static function whatsApp() {
        return new CBase_ForwarderStaticClass(CVendor_WhatsApp::class);
    }

    /**
     * @return \CVendor_Figma|CBase_ForwarderStaticClass
     */
    public static function figma() {
        return new CBase_ForwarderStaticClass(CVendor_Figma::class);
    }

    /**
     * @return \CVendor_Qontak|CBase_ForwarderStaticClass
     */
    public static function qontak() {
        return new CBase_ForwarderStaticClass(CVendor_Qontak::class);
    }

    /**
     * @return \CVendor_Dropbox|CBase_ForwarderStaticClass
     */
    public static function dropbox() {
        return new CBase_ForwarderStaticClass(CVendor_Dropbox::class);
    }

    /**
     * @return \CVendor_Kataai|CBase_ForwarderStaticClass
     */
    public static function kataai() {
        return new CBase_ForwarderStaticClass(CVendor_Kataai::class);
    }
}
