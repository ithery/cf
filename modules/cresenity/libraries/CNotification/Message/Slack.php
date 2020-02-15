<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Client as HttpClient;

class CNotification_Message_Slack extends CNotification_MessageAbstract {

    public function send() {
        $client = new HttpClient();
    }

}
