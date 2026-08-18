<?php

use GuzzleHttp\Client as HttpClient;

class CNotification_Message_Slack extends CNotification_MessageAbstract {
    /**
     * @return mixed
     *
     * @todo implement Slack send
     */
    public function send() {
        $client = new HttpClient();
    }
}
