<?php

use GuzzleHttp\Client;

class CNotification_Message_Wago extends CNotification_MessageAbstract {
    public function send() {
        $token = $this->getOption('token', carr::get($this->config, 'token'));

        $endPoint = 'https://wa-go.id/api/device/message/send';
        if ($this->getOption('sandbox') == true) {
            $endPoint = 'https://wapro.dev.ittron.co.id/api/device/message/send';
        }
        $message = $this->getOption('message');
        $imageUrl = $this->getOption('imageUrl');
        $recipient = $this->getOption('recipient');

        $client = new Client();
        $response = $client->post($endPoint, [
            'headers' => [
                'Authorization' => "Bearer ${token}"
            ],
            'form_params' => [
                'phone' => $recipient,
                'message' => $message,
                'imageUrl' => $imageUrl,
            ],
        ]);

        $result = [];
        $result['response'] = json_decode($response->getBody());

        return $result;
    }
}
