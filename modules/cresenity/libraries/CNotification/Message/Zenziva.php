<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Oct 2, 2020
 */
class CNotification_Message_Zenziva extends CNotification_MessageAbstract {
    public function send() {
        $userKey = carr::get($this->config, 'key');
        $userPass = carr::get($this->config, 'secret');

        $message = $this->getOption('message');
        $msisdn = $this->getOption('recipient');
        $text = urlencode($message);
        $url = 'https://reguler.zenziva.net/apps/smsapi.php?userkey=' . $userKey . '&passkey=' . $userPass . '&nohp=' . $msisdn . '&pesan=' . $text;
        $curl = CCurl::factory($url);
        $curl->setSSL();
        $response = $curl->exec()->response();

        if (!preg_match('#<status>0</status>#ims', $response, $matches)) {
            $exceptionRequest = new Exception('Error from SMS Response:' . $response);
            throw $exceptionRequest;
        }
        $return = [];
        $return['request'] = $url;
        $return['response'] = $response;
        return $return;
    }
}
