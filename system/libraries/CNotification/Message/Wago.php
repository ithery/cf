<?php

class CNotification_Message_Wago extends CNotification_MessageAbstract {
    public function send() {
        $token = $this->getOption('token', carr::get($this->config, 'token'));

        $message = $this->getOption('message');
        $imageUrl = $this->getOption('imageUrl');
        $scheduleAt = $this->getOption('scheduleAt');
        $recipient = $this->getOption('recipient');

        $device = CVendor::wago()->device($token, ['sandbox' => $this->getOption('sandbox', false)]);
        $result = [];
        $options = [];
        if ($imageUrl) {
            $options['imageUrl'] = $imageUrl;
        }
        if ($scheduleAt) {
            $options['scheduleAt'] = $scheduleAt;
        }
        $result['response'] = $device->sendMessage($recipient, $message, $options);

        return $result;
    }
}
