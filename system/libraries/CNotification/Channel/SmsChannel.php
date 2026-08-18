<?php

class CNotification_Channel_SmsChannel extends CNotification_ChannelAbstract {
    /**
     * @param array $config
     */
    public function __construct($config = []) {
        parent::__construct($config);
        $this->channelName = 'Sms';
    }

    /**
     * @param mixed $data
     * @param mixed $logNotificationModel
     *
     * @return mixed
     */
    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);

        return $message->send();
    }
}
