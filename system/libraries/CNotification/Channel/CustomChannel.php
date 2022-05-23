<?php

class CNotification_Channel_CustomChannel extends CNotification_ChannelAbstract {
    /**
     * @var \Opis\Closure\SerializableClosure
     */
    protected $messageHandler = null;

    public function __construct($config = []) {
        parent::__construct($config);

        $this->channelName = carr::get($config, 'channel', 'Custom');
    }

    /**
     * @param Closure $messageHandler
     *
     * @return $this
     */
    public function setMessageHandler($messageHandler) {
        $this->messageHandler = new \Opis\Closure\SerializableClosure($messageHandler);

        return $this;
    }

    protected function handleMessage($data, $logNotificationModel) {
        return $this->messageHandler->__invoke($data, $logNotificationModel);
    }
}
