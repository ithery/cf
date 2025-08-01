<?php

abstract class CVendor_Namecheap_AbstractCommand implements CVendor_Namecheap_CommandInterface {
    protected $api;

    public function __construct($api) {
        $this->api = $api;
    }
}
