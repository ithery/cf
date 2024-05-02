<?php

class CEmail_Driver_MailersendDriver extends CEmail_DriverAbstract {
    public function send(array $to, $subject, $body, $options = []) {
        $apiKey = $this->config->getPassword();
        $errCode = 0;
        $errMessage = '';
        $from = carr::get($options, 'from', $this->config->getFrom());
        $fromName = carr::get($options, 'from_name', $this->config->getFromName());
        $mailersend = new CVendor_MailerSend(['api_key' => $apiKey]);
        $bulkEmailParams = [];

        if (!$from) {
            $errCode++;
            $errMessage = 'From Empty';
        }
        if (!$fromName) {
            $errCode++;
            $errMessage = 'From Name Empty';
        }
        if (!$errCode) {
            foreach ($to as $t) {
                if (filter_var($t, FILTER_VALIDATE_EMAIL)) {
                    $bulkEmailParams[] = (new CVendor_MailerSend_Helpers_Builder_EmailParams())
                        ->setFrom($from)
                        ->setFromName($fromName)
                        ->setRecipients([
                            new CVendor_MailerSend_Helpers_Builder_Recipient($t, ''),
                            ])
                        ->setSubject($subject)
                        ->setHtml($body);
                }
            }
        }
        if (!$errCode && !$bulkEmailParams) {
            $errCode++;
            $errMessage = 'No Recipients';
        }
        if (!$errCode) {
            $response = $mailersend->bulkEmail->send($bulkEmailParams);
            $statusCode = carr::get($response, 'status_code');
            if ($statusCode != 202) {
                $responseBody = carr::get($response, 'body', []);

                throw new Exception('Fail to send mail, API Response:(' . $statusCode . ')' . json_encode($responseBody));
            }
        } else {
            $response = [
                'errCode'=>$errCode,
                'errMessage'=>$errMessage,
            ];
        }

        return $response;
    }
}
