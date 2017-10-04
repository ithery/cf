<?php

class celasticmail {

    public function send($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {

        $smtp_password = carr::get($options, 'smtp_password');
        $smtp_host = carr::get($options, 'smtp_host');
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtp_host) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if ($smtp_host != 'smtp.sendgrid.net') {
            throw new Exception('Fail to send mail API, SMTP Host is not valid');
        }
        $sendgrid_apikey = $smtp_password;
        $smtp_from = carr::get($options, 'smtp_from');
        if ($smtp_from == null) {
            $smtp_from = ccfg::get('smtp_from');
        }
        $smtp_from_name = carr::get($options, 'smtp_from_name');
        if ($smtp_from_name == null) {
            $smtp_from_name = ccfg::get('smtp_from_name');
        }

        $url = 'https://api.elasticemail.com/v2/email/send';

        try {
            if (!is_array($to)) {
                $to = array($to);
            }
            $to_implode = implode(";", $to);
            $post = array('from' => $smtp_from,
                'fromName' => $smtp_from_name,
                'apikey' => $smtp_password,
                'subject' => $subject,
                'to' => $to_implode,
                'bodyHtml' => $message,
                'isTransactional' => false
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $result = curl_exec($ch);
            curl_close($ch);

            echo $result;
        } catch (Exception $ex) {
            return $ex;
        }
        return true;
    }

}
