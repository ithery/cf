<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CVendor_Namecheap_AbstractCommand implements CVendor_Namecheap_CommandInterface {
    protected $api;
    
    public function __construct($api) {
        $this->api = $api;
    }
}