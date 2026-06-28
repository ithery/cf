<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Server {
    /**
     * @var null|CRemote_SSH
     */
    private $ssh;

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $ssh
     */
    public function __construct($ssh = null) {
        if ($ssh instanceof CRemote_SSH_Config) {
            $ssh = new CRemote_SSH($ssh);
        }
        $this->ssh = $ssh;
    }

    /**
     * @return null|CRemote_SSH
     */
    public function getSSH() {
        return $this->ssh;
    }

    /**
     * @return bool
     */
    public function isRemote() {
        return $this->ssh !== null;
    }

    /**
     * @return bool
     */
    public function isLocal() {
        return $this->ssh === null;
    }
}
