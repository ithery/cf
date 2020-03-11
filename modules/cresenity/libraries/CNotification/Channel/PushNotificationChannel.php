<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CNotification_Channel_PushNotificationChannel extends CNotification_ChannelAbstract {

    protected static $channelName = 'PushNotification';

    protected function handleMessage($data, $logNotificationModel) {
        $message = $this->createMessage($data);
        
       
        return $message->send();
    }

    

}
