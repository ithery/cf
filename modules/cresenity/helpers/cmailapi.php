<?php

class cmailapi {

    public static function sendgridv3($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
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

        if (is_array($to)) {
            $to = carr::get($to, 0);
        }
        $from = new CSendGrid_Email($smtp_from_name, $smtp_from);

        $toSendGrid = array();
        if (!is_array($to)) {
            $to = array($to);
        }
        foreach ($to as $toItem) {
            $toSendGrid[] = new CSendGrid_Email(null, $toItem);
        }
        $content = new CSendGrid_Content("text/html", $message);



        $mail = new CSendGrid_Mail($from, $subject, $toSendGrid, $content);
        foreach ($attachments as $att) {
            $filename = basename($att);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $path = $att;
            $type = 'application/text';
            if ($ext == 'pdf') {
                $type = 'application/pdf';
            }
            if ($ext == 'jpg' || $ext == 'jpeg') {
                $type = 'image/jpeg';
            }
            if ($ext == 'png') {
                $type = 'image/png';
            }
            $attachment = new CSendGrid_Attachment();
            $attachment->setContent(base64_encode(file_get_contents($att)));
            $attachment->setType($type);
            $attachment->setDisposition("attachment");
            $attachment->setFilename($filename);
            $mail->addAttachment($attachment);
        }


        $sg = new CSendGrid($sendgrid_apikey);


        //cdbg::var_dump(json_encode($mail, JSON_PRETTY_PRINT));

        $response = $sg->client->mail()->send()->post($mail);

        if ($response->statusCode() > 400) {
            throw new Exception('Fail to send mail, API Response:(' . $response->statusCode() . ')' . $response->body());
        }
        return $response;
    }

    public static function sendgrid($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
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

        $files = array();

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

        if (count($files) > 0) {
            //curl_setopt($session, CURLOPT_SAFE_UPLOAD, false);
        }
        // obtain response
        $response = curl_exec($session);
        curl_close($session);

        $response_array = json_decode($response, true);
        if (carr::get($response_array, 'message') != 'success') {
            throw new Exception('Fail to send mail, API Response:' . $response);
        }
        return $response_array;
    }

    public static function mailgun($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
        //$sendgrid_apikey = "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";
        //public key: pubkey-c338bfc1568e4d6e79331119e6c56645
        //private key: key-5f194bedfdade1fa513910895857d447
        $smtp_password = carr::get($options, 'smtp_password');
        $smtp_host = carr::get($options, 'smtp_host');
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtp_host) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if ($smtp_host != 'smtp.mailgun.org') {
            throw new Exception('Fail to send mail API, SMTP Host is not valid Mailgun SMTP');
        }
        $mailgun_apikey = $smtp_password;
        $smtp_from = carr::get($options, 'smtp_from');
        if ($smtp_from == null) {
            $smtp_from = ccfg::get('smtp_from');
        }
        $smtp_from_name = carr::get($options, 'smtp_from_name');
        if ($smtp_from_name == null) {
            $smtp_from_name = ccfg::get('smtp_from_name');
        }


        $smtp_domain = carr::get($options, 'smtp_domain');
        if ($smtp_domain == null) {
            $smtp_domain = ccfg::get('smtp_domain');
        }
        if ($smtp_domain == null) {
            $smtp_domain = 'mg.compro.id';
        }
        $url = 'https://api.mailgun.net/v3/' . $smtp_domain . '/messages';
        $pass = $mailgun_apikey;
        /*
          $template_id = '<your_template_id>';
          $js = array(
          'sub' => array(':name' => array('Elmer')),
          'filters' => array('templates' => array('settings' => array('enable' => 1, 'template_id' => $template_id)))
          );
         */

        $files = array();

        $params = array(
            'to' => $to,
            'from' => $smtp_from,
            'subject' => $subject . '',
            'html' => $message,
        );


        if (count($cc) > 0) {
            $params['cc'] = $cc;
        }
        if (count($bcc) > 0) {
            $params['bcc'] = $bcc;
        }


        if (isset($_GET['debug2'])) {
            cdbg::var_dump($url);
        }
        // Generate curl request
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:' . $mailgun_apikey);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        // Tell curl to use HTTP POST
        curl_setopt($session, CURLOPT_POST, true);
        // Tell curl that this is the body of the POST
        curl_setopt($session, CURLOPT_POSTFIELDS, curl::as_post_string($params));
        // Tell curl not to return headers, but do return the response
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        if (count($files) > 0) {
            //curl_setopt($session, CURLOPT_SAFE_UPLOAD, false);
        }
        // obtain response
        $response = curl_exec($session);
        curl_close($session);

        $response_array = json_decode($response, true);
        if (strlen(carr::get($response_array, 'id')) == 0) {
            throw new Exception('Fail to send mail, API Response:' . $response);
        }
        return $response_array;
    }

    public static function elasticemail($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {

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
            if (!is_array($cc)) {
                $cc = array($cc);
            }
            if (!is_array($bcc)) {
                $bcc = array($bcc);
            }

            $to_implode = implode(";", $to);
            $cc_implode = implode(";", $cc);
            $bcc_implode = implode(";", $bcc);
            $post = array('from' => $smtp_from,
                'fromName' => $smtp_from_name,
                'apikey' => $smtp_password,
                'subject' => $subject . '[API]',
                'msgTo' => $to_implode,
                'msgCC' => $cc_implode,
                'msgBcc' => $bcc_implode,
                'bodyHtml' => $message,
                'isTransactional' => true
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

            $response = curl_exec($ch);
            curl_close($ch);

            $response_array = json_decode($response, true);
            if (!carr::get($response_array, 'success')) {
                throw new Exception('Fail to send mail, API Response:' . $response);
            }
        } catch (Exception $ex) {
            return $ex;
        }
        return true;
    }

    public static function postmark($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {


        $smtp_password = carr::get($options, 'smtp_password');
        $smtp_host = carr::get($options, 'smtp_host');
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtp_host) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if ($smtp_host != 'smtp.postmarkapp.com') {
            throw new Exception('Fail to send mail API, SMTP Host is not valid');
        }

        $smtp_from = carr::get($options, 'smtp_from');
        if ($smtp_from == null) {
            $smtp_from = ccfg::get('smtp_from');
        }
        $smtp_from_name = carr::get($options, 'smtp_from_name');
        if ($smtp_from_name == null) {
            $smtp_from_name = ccfg::get('smtp_from_name');
        }

        $url = 'https://api.postmarkapp.com/email';

        try {
            if (!is_array($to)) {
                $to = array($to);
            }
            if (!is_array($cc)) {
                $cc = array($cc);
            }
            if (!is_array($bcc)) {
                $bcc = array($bcc);
            }

            $to_implode = implode(",", $to);
            $cc_implode = implode(",", $cc);
            $bcc_implode = implode(",", $bcc);
            $post = array();
            $post['From'] = $smtp_from;
            $post['To'] = $to_implode;
            $post['Cc'] = $cc_implode;
            $post['Bcc'] = $bcc_implode;
            $post['Subject'] = $subject;
            $post['HtmlBody'] = $message;

            $version = phpversion();
            $os = PHP_OS;

            $json = json_encode($post);
            cdbg::var_dump($json);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'User-Agent: Postmark-PHP (PHP Version:' . $version . ', OS:' . $os . ')',
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'X-Postmark-Server-Token:' . $smtp_password
                ),
            ));



            $response = curl_exec($ch);
            curl_close($ch);
            cdbg::var_dump($response);
            die;
            $response_array = json_decode($response, true);
            if (!carr::get($response_array, 'success')) {
                throw new Exception('Fail to send mail, API Response:' . $response);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }

}
