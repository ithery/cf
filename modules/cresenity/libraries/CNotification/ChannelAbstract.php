<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CNotification_ChannelAbstract implements CNotification_ChannelInterface {

    protected static $channelName;

    public function queue($className, array $options) {
        $options = [
            'channel' => static::$channelName,
            'className' => $className,
            'options' => $options,
        ];

        $taskQueue = CNotification_TaskQueue_NotificationSender::dispatch($options);
    }

    public function send($className, array $options) {

        $message = new $className();
        $message->setOptions($options);
        $result = $message->execute();

        $messageResult = $this->handleMessage($message, $result);
        return $messageResult;
    }

    
    
}
