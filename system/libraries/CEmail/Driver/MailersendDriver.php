<?php

class CEmail_Driver_MailersendDriver extends CEmail_DriverAbstract {
    public function send(array $to, $subject, $body, $options = []) {
        $apiKey = $this->config->getPassword();

        $from = carr::get($options, 'from', $this->config->getFrom());
        $fromName = carr::get($options, 'from_name', $this->config->getFromName());

        $domain = carr::get($options, 'domain', carr::get($options, 'smtp_domain'));

        $url = 'https://api.mailersend.com/v1/email';

        $files = [];
        $paramTo = [];
        foreach ($to as $t) {
            $paramTo[]=[
                'email'=>$t,
            ];
        }
        $params = [
            'to' => $paramTo,
            'from' => [
                'email'=>$from,
            ],
            'subject' => $subject . '',
            'html' => $body,
        ];

        $cc = carr::get($options, 'cc');
        $bcc = carr::get($options, 'bcc');
        if (is_array($cc) && count($cc) > 0) {
            $params['cc'] = $cc;
        }
        if (is_array($cc) && count($bcc) > 0) {
            $params['bcc'] = $bcc;
        }

        // Generate curl request
        $paramsJson = json_encode($params);
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_POSTFIELDS, json_encode($paramsJson));
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Requested-With: XMLHttpRequest',
            'Authorization: Bearer '.$apiKey
        ]);
        // obtain response
        $response = curl_exec($session);
        // cdbg::varDump($paramsJson);
        // cdbg::varDump($response);

        curl_close($session);

        $responseArray = json_decode($response, true);
        if (strlen(carr::get($responseArray, 'id')) == 0) {
            throw new Exception('Fail to send mail, API Response:' . $response);
        }

        return $responseArray;
    }
}
