<?php

class CNotification {
    /**
     * @return CNotification_Manager
     */
    public static function manager() {
        return CNotification_Manager::instance();
    }

    /**
     * @return CNotification_Channel_EmailChannel
     */
    public static function email() {
        return static::manager()->channel('Email');
    }

    /**
     * @return CNotification_Channel_PushNotificationChannel
     */
    public static function pushNotification() {
        return static::manager()->channel('PushNotification');
    }

    /**
     * @return CNotification_Channel_SmsChannel
     */
    public static function sms() {
        return static::manager()->channel('Sms');
    }

    /**
     * @return CNotification_Channel_WhatsappChannel
     */
    public static function whatsapp() {
        return static::manager()->channel('Whatsapp');
    }

    /**
     * @return CNotification_Channel_DatabaseChannel
     */
    public static function database() {
        return static::manager()->channel('Database');
    }
}
