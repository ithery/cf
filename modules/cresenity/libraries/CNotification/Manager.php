<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CNotification_Manager {

    protected $channels;
    protected $vendors;
    protected static $instance;

    /**
     * 
     * @return CNotification_Manager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CNotification_Manager();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->channels = [];
    }

    /**
     * 
     * @param string $channel
     * @param array $config
     * @return \CNotification_ChannelAbstract
     */
    public function channel($channel, $config = null) {
        $className = 'CNotification_Channel_' . $channel . 'Channel';
        if ($config != null) {
            return new $className($config);
        }
        if (!isset($this->channels[$channel])) {
            return new $className();
        }
        return $this->channels[$channel];
    }

    /**
     * 
     * @param string $vendor
     * @param array $config
     * @return \CNotification_VendorAbstract
     */
    public function vendor($vendor, $config) {
        $vendorClass = $this->toVendorClass($vendor);
        $className = 'CNotification_Vendor_' . $vendorClass . '';
        if ($config != null) {
            return new $className($config);
        }
        if (!isset($this->vendors[$vendorClass])) {
            return new $className();
        }
        return $this->vendors[$vendorClass];
    }

    /**
     * 
     * @param string $vendor
     * @return string
     */
    protected function toVendorClass($vendor) {
        switch ($vendor) {
            case 'sendgrid':
                return 'SendGrid';
        }
        return ucfirst(cstr::camel($vendor));
    }

    /**
     * 
     * @return string
     */
    public function logNotificationModelName() {
        return CF::config('notification.log_notification_model', CNotification_Model_LogNotification::class);
    }

    /**
     * 
     * @return \CModel
     */
    public function createLogNotificationModel() {
        $modelName = $this->logNotificationModelName();
        return new $modelName();
    }

}
