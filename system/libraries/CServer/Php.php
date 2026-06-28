<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Php {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getVersion() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo phpversion();"'));
        }

        return phpversion();
    }

    /**
     * @param string $extName
     *
     * @return string
     */
    public function getExtVersion($extName) {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo phpversion(\'' . $extName . '\');"'));
        }

        return phpversion($extName);
    }

    /**
     * @return array
     */
    public function getAllIniConfiguration() {
        if ($this->server->isRemote()) {
            $output = trim($this->server->runCommand('php -r "echo json_encode(ini_get_all());"'));

            return json_decode($output, true) ?: [];
        }

        return ini_get_all();
    }

    /**
     * @param string $extName
     *
     * @return array
     */
    public function getAllIniConfigurationExt($extName) {
        if ($this->server->isRemote()) {
            $output = trim($this->server->runCommand('php -r "echo json_encode(ini_get_all(\'' . $extName . '\'));"'));

            return json_decode($output, true) ?: [];
        }

        return ini_get_all($extName);
    }

    /**
     * @param string $varName
     *
     * @return string
     */
    public function getIniConfiguration($varName) {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo ini_get(\'' . $varName . '\');"'));
        }

        return ini_get($varName);
    }

    /**
     * @return string
     */
    public function getIniLoadedFile() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo php_ini_loaded_file();"'));
        }

        return php_ini_loaded_file();
    }

    /**
     * @return string
     */
    public function getSapiName() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo php_sapi_name();"'));
        }

        return php_sapi_name();
    }

    /**
     * @return string
     */
    public function getTempDir() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('php -r "echo sys_get_temp_dir();"'));
        }

        return sys_get_temp_dir();
    }

    /**
     * @return string
     */
    public function getCurrentUser() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('whoami'));
        }

        return get_current_user();
    }
}
