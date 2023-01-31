<?php

class CNotification_Message_Watzap extends CNotification_MessageAbstract {
    public function send() {
        $apiKey = $this->getOption('apiKey', carr::get($this->config, 'api_key'));
        $numberKey = $this->getOption('numberKey', carr::get($this->config, 'number_key'));

        $message = $this->getOption('message');
        $recipient = $this->getOption('recipient');

        $device = CVendor::watzap()->device($numberKey, $apiKey);

        $response = null;
        if (cstr::endsWith($recipient, '@g.us')) {
            $result['response'] = $device->groupSendMessage($recipient, $message);
        } else {
            $result['response'] = $device->sendMessage($recipient, $message);
        }
        $result = [
            'response' => $response
        ];

        return $result;
    }
}
