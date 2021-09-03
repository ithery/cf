<?php

use GuzzleHttp\Client as HttpClient;

class CNotification_Message_Slack extends CNotification_MessageAbstract {
    public function send() {
        $client = new HttpClient();
    }
}
