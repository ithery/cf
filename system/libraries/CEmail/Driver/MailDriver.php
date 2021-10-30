<?php

class CEmail_Driver_MailDriver extends CEmail_DriverAbstract {
    public function send(array $to, $subject, $body, $options = []) {
        $addresses = $this->formatAddresses($to);
        $returnPath = carr::get($options, 'returnPath', carr::get($options, 'from'));
        $headers = [];

        foreach (['cc' => 'Cc', 'bcc' => 'Bcc', 'reply_to' => 'Reply-To'] as $key => $headerKey) {
            $value = $this->formatAddresses(carr::get($options, $key));
            $headers[$headerKey] = $value;
        }
        $response = @mail($addresses, $subject, $body, $headers, '-oi -f ' . $returnPath);
        if ($response === false) {
            throw new \CEmail_Exception_EmailSendingFailedException('Failed sending email');
        }

        return $response;
    }
}
