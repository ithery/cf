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
    public function channel($channel) {
        if (!isset($this->channels[$channel])) {
            $className = 'CNotification_Channel_' . $channel.'Channel';
            return new $className();
        }
        return $this->channels[$channel];
    }

}
