<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_NotificationCouldNotBeSentException extends Exception {

    public static function noNotificationClassForEvent($event) {
        $eventClass = get_class($event);
        return new static("There is no notification class that can handle event `{$eventClass}`.");
    }

}
