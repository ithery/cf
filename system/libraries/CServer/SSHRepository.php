<?php

class CServer_SSHRepository {
    protected $sshArray = [];

    private static $instance;

    private function __construct() {
        $this->sshArray = [];
    }

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new CServer_SSHRepository();
        }

        return self::$instance;
    }

    public function getSSH($sshConfig) {
        $ssh = null;
        if ($sshConfig instanceof CRemote_SSH) {
            $ssh = $sshConfig;
            $sshConfig = $sshConfig->getConfig();
        }

        $host = carr::get($sshConfig, 'host', carr::get($sshConfig, 'ip_address'));
        if (!isset($this->sshArray[$host])) {
            if ($ssh == null) {
                $ssh = CRemote::ssh($sshConfig);
            }
            $this->sshArray[$host] = $ssh;
        }

        return $this->sshArray[$host];
    }
}
