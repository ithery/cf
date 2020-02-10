<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CNotification_Message_Firebase extends CNotification_MessageAbstract {

    public function send() {


        $firebase = CVendor::firebase(carr::get($this->config, 'key'), carr::except($this->config, ['key']));
        $tokens = carr::wrap($this->getOption('recipient'));

        
        $data = $this->getOption('data');
        $messaging = $firebase->createMessaging();

        $message = $messaging->createCloudMessage()
                ->withNotification($messaging->createNotification($this->getOption('subject'), $this->getOption('message')));

        if (is_array($data)) {
            $message = $message->withData($data);
        }

        $messaging->sendMulticast($message, $tokens);
    }

}
