<?php

class CServer_Base {
    protected $sshConfig;
    protected $host;

    public function getSSHConfig() {
        return $this->sshConfig;
    }
}
