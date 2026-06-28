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

    /**
     * @return CServer_Storage
     */
    public function storage() {
        return CServer_Storage::instance($this->ssh);
    }

    /**
     * @return CServer_Php
     */
    public function php() {
        return CServer_Php::instance();
    }

    /**
     * @return CServer_Memory
     */
    public function memory() {
        return CServer_Memory::instance($this->ssh);
    }

    /**
     * @return CServer_System
     */
    public function system() {
        return CServer_System::instance($this->ssh);
    }

    /**
     * @return CServer_Command
     */
    public function command() {
        return CServer_Command::instance($this->ssh);
    }

    /**
     * @return CServer_Database
     */
    public function database() {
        return CServer_Database::instance();
    }

    /**
     * @return CServer_Config
     */
    public function config() {
        return CServer_Config::instance();
    }

    /**
     * @return CServer_PhpInfo
     */
    public function phpInfo() {
        return new CServer_PhpInfo();
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function runCommand($command) {
        if ($this->isRemote()) {
            $output = '';
            $this->ssh->run($command, function ($line) use (&$output) {
                $output .= $line . PHP_EOL;
            });

            return $output;
        }

        $result = '';
        exec($command . ' 2>&1', $outputLines, $exitCode);
        if (is_array($outputLines)) {
            $result = implode(PHP_EOL, $outputLines);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getHostname() {
        if ($this->isRemote()) {
            return trim($this->runCommand('hostname'));
        }

        return gethostname();
    }

    /**
     * @return string
     */
    public function getOS() {
        if ($this->isRemote()) {
            return trim($this->runCommand('uname -s'));
        }

        return CServer::getOS();
    }
}
