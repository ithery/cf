<?php

abstract class CNotification_MethodAbstract implements CNotification_MethodInterface {
    use CTrait_HasOptions;

    public function __construct() {
    }

    /**
     * @param mixed $logNotificationModel
     *
     * @return void
     */
    public function onNotificationSent($logNotificationModel) {
    }
}
