<?php

class CNotification_Channel_CustomChannel extends CNotification_ChannelAbstract {
    /**
     * @param array $config
     */
    public function __construct($config = []) {
        parent::__construct($config);

        $this->channelName = carr::get($config, 'channel', 'Custom');
    }

    /**
     * @param mixed $data
     * @param mixed $logNotificationModel
     *
     * @return mixed
     */
    protected function handleMessage($data, $logNotificationModel) {
        return $this->messageHandler->__invoke($data, $logNotificationModel);
    }
}
