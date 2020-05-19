<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Support_RemoteFile {

    protected $key;
    protected $disk;

    public function __construct($key, $disk) {
        $this->key = $key;

        $this->disk = $disk;
    }

    public function getKey() {
        return $this->key;
    }

    public function getDisk() {
        return $this->disk;
    }

    public function getFilename() {
        return basename($this->key);
    }

    public function getName() {
        return pathinfo($this->getFilename(), PATHINFO_FILENAME);
    }

}
