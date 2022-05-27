<?php

class CNotification_Channel_DatabaseChannel extends CNotification_ChannelAbstract {
    public function __construct($config = []) {
        parent::__construct($config);
        $this->channelName = 'Database';
    }

    protected function handleMessage($data, $logNotificationModel) {
        $message = new CNotification_Message_Database($this->config, $data);
        $message->setType($logNotificationModel->message_class);

        return $message->send();
    }
}
