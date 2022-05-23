<?php

class CNotification_Channel_WhatsappChannel extends CNotification_ChannelAbstract {
    public function __construct($config = []) {
        parent::__construct($config);
        $this->channelName = 'Whatsapp';
    }

    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);

        return $message->send();
    }
}
