<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Nexmo\Client as NexmoClient;

class CVendor_Nexmo {

    /**
     *
     * @var \Nexmo\Client 
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $from;
    public function __construct($apiKey, $apiSecret, $options = []) {
        $this->client = new NexmoClient(
                new Nexmo\Client\Credentials\Basic(
                $apiKey, $apiSecret
                )
        );
        $this->from = carr::get($options,'from');
    }

    /**
     * 
     * @return \Nexmo\Client
     */
    public function getClient() {
        return $this->client;
    }

    
    /**
     * 
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }
}
