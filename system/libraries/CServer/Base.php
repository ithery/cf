<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CServer_Base {

    protected $sshConfig;
    protected $host;

    public function getSSHConfig() {
        return $this->sshConfig;
    }

}
