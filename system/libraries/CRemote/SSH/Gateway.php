<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */

use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\System\SSH\Agent;

class CRemote_SSH_Gateway implements CRemote_SSH_GatewayInterface {
    /**
     * @var CRemote_SSH_Config
     */
    protected $config;

    /**
     * @var \phpseclib3\Net\SFTP
     */
    protected $connection;

    /**
     * @param CRemote_SSH_Config $config
     */
    public function __construct(CRemote_SSH_Config $config) {
        $this->config = $config;
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function connect($username) {
        return $this->getConnection()->login($username, $this->getAuthForLogin());
    }

    /**
     * @return bool
     */
    public function connected() {
        return $this->getConnection()->isConnected();
    }

    /**
     * @param string $command
     * @param mixed  $callback
     *
     * @return string
     */
    public function run($command, $callback = null) {
        return $this->getConnection()->exec($command, $callback);
    }

    /**
     * @param string $remote
     * @param string $local
     *
     * @return void
     */
    public function get($remote, $local) {
        $this->getConnection()->get($remote, $local);
    }

    /**
     * @param string $remote
     *
     * @return string
     */
    public function getString($remote) {
        return $this->getConnection()->get($remote);
    }

    /**
     * @param string $remote
     *
     * @return int
     */
    public function getFilesize($remote) {
        return $this->getConnection()->filesize($remote);
    }

    /**
     * @param string $local
     * @param string $remote
     *
     * @return void
     */
    public function put($local, $remote) {
        $this->getConnection()->put($remote, $local, SFTP::SOURCE_LOCAL_FILE);
    }

    /**
     * @param string $remote
     * @param string $contents
     *
     * @return void
     */
    public function putString($remote, $contents) {
        $this->getConnection()->put($remote, $contents);
    }

    /**
     * @return \phpseclib3\Net\SFTP
     */
    public function getConnection() {
        if ($this->connection) {
            return $this->connection;
        }

        $host = $this->config->getConnectionHost();
        $port = $this->config->getPort();

        if (cstr::contains($host, ':')) {
            list($host, $port) = explode(':', $host);
            $port = (int) $port;
        }

        return $this->connection = new SFTP($host, $port, $this->config->getTimeout());
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return \phpseclib3\Crypt\Common\PrivateKey|\phpseclib3\System\SSH\Agent|string
     */
    protected function getAuthForLogin() {
        if ($this->config->getUseAgent()) {
            return new Agent();
        }
        if ($this->config->hasPrivateKey()) {
            return $this->loadPrivateKey();
        }
        if ($this->config->hasPassword()) {
            return $this->config->getPassword();
        }

        throw new \InvalidArgumentException('Password / key is required.');
    }

    /**
     * @return \phpseclib3\Crypt\Common\PrivateKey
     */
    protected function loadPrivateKey() {
        $keytext = $this->config->getPrivateKey();
        if ($keytext !== null && trim($keytext) !== '') {
            return PublicKeyLoader::loadPrivateKey(trim($keytext));
        }

        $keyPath = $this->config->getKeyPath();
        if ($keyPath !== null && trim($keyPath) !== '') {
            $keyContent = file_get_contents($keyPath);

            return PublicKeyLoader::loadPrivateKey(trim($keyContent));
        }

        throw new \InvalidArgumentException('No private key available');
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->config->getTimeout();
    }

    /**
     * @param int $timeout
     *
     * @return void
     */
    public function setTimeout($timeout) {
        $this->config->setTimeout($timeout);
        $this->connection = null;
        $this->getConnection();
    }

    /**
     * @param mixed $commands
     * @param int   $timeout
     *
     * @return string
     */
    public function runBlocking($commands, $timeout = 2) {
        $connection = $this->getConnection();
        $connection->write($commands);
        $connection->setTimeout($timeout);

        return $connection->read();
    }

    /**
     * @param string $remote
     *
     * @return bool
     */
    public function exists($remote) {
        return $this->getConnection()->file_exists($remote);
    }

    /**
     * @param string $remote
     * @param string $newRemote
     *
     * @return bool
     */
    public function rename($remote, $newRemote) {
        return $this->getConnection()->rename($remote, $newRemote);
    }

    /**
     * @param string $remote
     *
     * @return bool
     */
    public function delete($remote) {
        return $this->getConnection()->delete($remote);
    }

    /**
     * @return int|bool
     */
    public function status() {
        return $this->getConnection()->getExitStatus();
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->config->getConnectionHost();
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->config->getPort();
    }

    /**
     * @return string
     */
    public function getLog() {
        return $this->getConnection()->getLog();
    }

    /**
     * @return void
     */
    public function disconnect() {
        if ($this->connection) {
            $this->connection->disconnect();
        }
    }
}
