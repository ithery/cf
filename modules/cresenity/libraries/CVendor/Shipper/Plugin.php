<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Shipper_Plugin {

    use CTrait_HasOptions;

    protected $client;

    public function __construct($options = []) {
        $this->options = $options;
    }

    protected function createClient() {
        return new CVendor_Shipper_Plugin_Client($this->options);
    }

    public function client() {
        if ($this->client == null) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

}
