<?php

class CServer_Base {
    protected $sshConfig;

    protected $host;

    protected $ssh;

    public function getSSHConfig() {
        return $this->sshConfig;
    }

    public function setSSH($sshConfig) {
        $ssh = null;
        if ($sshConfig instanceof CRemote_SSH) {
            $ssh = $sshConfig;
            $sshConfig = $sshConfig->getConfig();
        }

        $this->ssh = $ssh;
        $this->sshConfig = $sshConfig;
        $this->host = carr::get($sshConfig, 'host', carr::get($sshConfig, 'ip_address'));
    }

    public function getSSH() {
        if ($this->ssh == null) {
            if ($this->sshConfig) {
                $this->ssh = CRemote::ssh($this->sshConfig);
            }
        }

        return $this->ssh;
    }
}
