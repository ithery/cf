<?php

defined('SYSPATH') or die('No direct access allowed.');

class CNotification_Message_Zenziva extends CNotification_MessageAbstract {
    /**
     * @return array
     */
    public function send() {
        $userKey = carr::get($this->config, 'key');
        $userPass = carr::get($this->config, 'secret');

        $message = $this->getOption('message');
        $msisdn = $this->getOption('recipient');
        $otp = $this->getOption('otp');
        $smsMethod = $otp ? 'sendOTP/' : 'sendsms/';
        $url = 'https://console.zenziva.net/reguler/api/' . $smsMethod;

        $post = [
            'userkey' => $userKey,
            'passkey' => $userPass,
            'to' => $msisdn,
        ];
        if ($otp) {
            $post['kode_otp'] = $otp;
        } else {
            $post['message'] = $message;
        }

        $curl = CCurl::factory($url);
        $curl->setSSL();
        $curl->setRawPost($post);
        $response = $curl->exec()->response();

        if (preg_match('#<status>0</status>#ims', $response)) {
            throw new CNotification_Exception('Error from SMS Response:' . $response);
        }

        return [
            'request' => $url,
            'response' => $response,
        ];
    }
}
