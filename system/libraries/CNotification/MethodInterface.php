<?php

interface CNotification_MethodInterface {
    /**
     * @param mixed $logNotificationModel
     *
     * @return void
     */
    public function onNotificationSent($logNotificationModel);
}
