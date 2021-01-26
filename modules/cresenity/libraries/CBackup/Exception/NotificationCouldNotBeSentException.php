<?php

class CBackup_Exception_NotificationCouldNotBeSentException extends Exception {
    public static function noNotificationClassForEvent($event) {
        $eventClass = get_class($event);
        return new static("There is no notification class that can handle event `{$eventClass}`.");
    }
}
