<?php

class CNotification_Channel_CustomChannel extends CNotification_ChannelAbstract {
    public function __construct($config = []) {
        parent::__construct($config);

        $this->channelName = carr::get($config, 'channel', 'Custom');
    }

    protected function handleMessage($data, $logNotificationModel) {
        return $this->messageHandler->__invoke($data, $logNotificationModel);
    }
}
