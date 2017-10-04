<?php

class cmailapi {

    public function sendgrid($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
        //$sendgrid_apikey = "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";

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

        $url = 'https://api.sendgrid.com/';
        $pass = $sendgrid_apikey;
        /*
          $template_id = '<your_template_id>';
          $js = array(
          'sub' => array(':name' => array('Elmer')),
          'filters' => array('templates' => array('settings' => array('enable' => 1, 'template_id' => $template_id)))
          );
         */



        $params = array(
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'from' => $smtp_from,
            'fromname' => $smtp_from_name,
            'subject' => $subject . '',
            'html' => $message,
        );

        $request = $url . 'api/mail.send.json';

        // Generate curl request
        $session = curl_init($request);
        // Tell PHP not to use SSLv3 (instead opting for TLS)
        curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sendgrid_apikey));
        // Tell curl to use HTTP POST
        curl_setopt($session, CURLOPT_POST, true);
        // Tell curl that this is the body of the POST
        curl_setopt($session, CURLOPT_POSTFIELDS, curl::as_post_string($params));
        // Tell curl not to return headers, but do return the response
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // obtain response
        $response = curl_exec($session);
        curl_close($session);

        $response_array = json_decode($response, true);
        if (carr::get($response_array, 'message') != 'success') {
            throw new Exception('Fail to send mail, API Response:' . $response);
        }
        return true;
    }

    public function elasticemail($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
        
        $smtp_password = carr::get($options, 'smtp_password');
        $smtp_host = carr::get($options, 'smtp_host');
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtp_host) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if ($smtp_host != 'smtp.elasticemail.com' && $smtp_host != 'smtp25.elasticemail.com') {
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
                'subject' => $subject.'[API]',
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
