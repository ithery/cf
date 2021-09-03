<?php

class CNotification_Channel_SmsChannel extends CNotification_ChannelAbstract {
    protected static $channelName = 'Sms';

    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);

        return $message->send();
    }
}
