<?php

abstract class CNotification_MethodAbstract implements CNotification_MethodInterface {
    use CTrait_HasOptions;

    public function __construct() {
    }

    public function onNotificatonSent($logNotificationModel) {
    }
}
