<?php

class CNotification {
    /**
     * @return CNotification_Manager
     */
    public static function manager() {
        return CNotification_Manager::instance();
    }

    /**
     * @return CNotification_ChannelAbstract
     */
    public static function email() {
        return static::manager()->channel('Email');
    }

    public static function pushNotification() {
        return static::manager()->channel('PushNotification');
    }

    public static function sms() {
        return static::manager()->channel('Sms');
    }
}
