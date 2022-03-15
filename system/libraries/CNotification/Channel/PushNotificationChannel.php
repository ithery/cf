<?php

class CNotification_Channel_PushNotificationChannel extends CNotification_ChannelAbstract {
    public function __construct($config = []) {
        parent::__construct($config);
        $this->channelName = 'PushNotification';
    }

    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);

        return $message->send();
    }
}
