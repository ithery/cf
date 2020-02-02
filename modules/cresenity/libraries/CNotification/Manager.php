<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CNotification_Manager {

    protected $channels;
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
