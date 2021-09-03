<?php

class CNotification_Channel_PushNotificationChannel extends CNotification_ChannelAbstract {
    protected static $channelName = 'PushNotification';

    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);

        return $message->send();
    }
}
