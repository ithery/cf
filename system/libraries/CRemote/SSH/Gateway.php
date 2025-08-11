<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */

use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use phpseclib3\System\SSH\Agent;

class CRemote_SSH_Gateway implements CRemote_SSH_GatewayInterface {
    /**
     * The host name of the server.
     *
     * @var string
     */
    protected $host;

    /**
     * The SSH port on the server.
     *
     * @var int
     */
    protected $port = 22;

    /**
     * The timeout for commands.
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * The authentication credential set.
     *
     * @var array
     */
    protected $auth;

    /**
     * The SecLib connection instance.
     *
     * @var \phpseclib3\Net\SFTP
     */
    protected $connection;

    /**
     * Create a new gateway implementation.
     *
     * @param string $host
     * @param mixed  $port
     * @param array  $auth
     * @param        $timeout
     */
    public function __construct($host, $port, array $auth, $timeout) {
        $this->auth = $auth;
        $this->setHostAndPort($host, $port);
        $this->setTimeout($timeout);
    }

    /**
     * Set the host and port from a full host string.
     *
     * @param string $host
     * @param string $port
     *
     * @return void
     */
    protected function setHostAndPort($host, $port) {
        $this->port = $port;
        if (!cstr::contains($host, ':')) {
            $this->host = $host;
        } else {
            list($this->host, $this->port) = explode(':', $host);

            $this->port = (int) $this->port;
        }
    }

    /**
     * Connect to the SSH server.
     *
     * @param string $username
     *
     * @return bool
     */
    public function connect($username) {
        return $this->getConnection()->login($username, $this->getAuthForLogin());
    }

    /**
     * Determine if the gateway is connected.
     *
     * @return bool
     */
    public function connected() {
        return $this->getConnection()->isConnected();
    }

    /**
     * Run a command against the server (non-blocking).
     *
     * @param string $command
     * @param mixed  $callback
     *
     * @return string
     */
    public function run($command, $callback = null) {
        $connection = $this->getConnection();

        return $this->getConnection()->exec($command, $callback);
    }

    /**
     * Download the contents of a remote file.
     *
     * @param string $remote
     * @param string $local
     *
     * @return void
     */
    public function get($remote, $local) {
        $this->getConnection()->get($remote, $local);
    }

    /**
     * Get the contents of a remote file.
     *
     * @param string $remote
     *
     * @return string
     */
    public function getString($remote) {
        return $this->getConnection()->get($remote);
    }

    /**
     * Get the contents of a remote file.
     *
     * @param string $remote
     *
     * @return string
     */
    public function getFilesize($remote) {
        return $this->getConnection()->filesize($remote);
    }

    /**
     * Upload a local file to the server.
     *
     * @param string $local
     * @param string $remote
     *
     * @return void
     */
    public function put($local, $remote) {
        $this->getConnection()->put($remote, $local, SFTP::SOURCE_LOCAL_FILE);
    }

    /**
     * Upload a string to to the given file on the server.
     *
     * @param string $remote
     * @param string $contents
     *
     * @return void
     */
    public function putString($remote, $contents) {
        $this->getConnection()->put($remote, $contents);
    }

    /**
     * Get the underlying SFTP connection.
     *
     * @return \phpseclib3\Net\SFTP
     */
    public function getConnection() {
        if ($this->connection) {
            return $this->connection;
        }

        return $this->connection = new SFTP($this->host, $this->port, $this->timeout);
    }

    /**
     * /**
     * Get the authentication object for login.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Crypt_RSA|\System_SSH_Agent|string
     */
    protected function getAuthForLogin() {
        if ($this->useAgent()) {
            return $this->getAgent();
        } elseif ($this->hasRsaKey()) {
            // If a "key" was specified in the auth credentials, we will load it into a
            // secure RSA key instance, which will be used to connect to the servers
            // in place of a password, and avoids the developer specifying a pass.
            return $this->loadRsaKey($this->auth);
        } elseif (isset($this->auth['password'])) {
            // If a plain password was set on the auth credentials, we will just return
            // that as it can be used to connect to the server. This will be used if
            // there is no RSA key and it gets specified in the credential arrays.
            return $this->auth['password'];
        }

        throw new \InvalidArgumentException('Password / key is required.');
    }

    /**
     * Determine if the SSH Agent should provide an RSA key.
     *
     * @return bool
     */
    protected function useAgent() {
        return isset($this->auth['agent']) && $this->auth['agent'] === true;
    }

    /**
     * Get a new SSH Agent instance.
     *
     * @return \phpseclib3\System\SSH\Agent
     */
    public function getAgent() {
        return new Agent();
    }

    /**
     * Determine if an RSA key is configured.
     *
     * @return bool
     */
    protected function hasRsaKey() {
        $hasKey = (isset($this->auth['key']) && trim($this->auth['key']) != '');

        return $hasKey || (isset($this->auth['keytext']) && trim($this->auth['keytext']) != '');
    }

    /**
     * Load the RSA key instance.
     *
     * @param array $auth
     *
     * @return \phpseclib3\Crypt\RSA
     */
    protected function loadRsaKey(array $auth) {
        $key = $this->getKey($auth);

        return $key;
    }

    /**
     * Create a new RSA key instance.
     *
     * @param array $auth
     *
     * @return \phpseclib3\Crypt\RSA
     */
    protected function getKey(array $auth) {
        $key = RSA::loadPrivateKey(trim(carr::get($auth, 'keytext')));

        return $key;
    }

    /**
     * Read the contents of the RSA key.
     *
     * @param array $auth
     *
     * @return string
     */
    protected function readRsaKey(array $auth) {
        if (isset($auth['key'])) {
            return $this->files->get($auth['key']);
        }

        return $auth['keytext'];
    }

    /**
     * Get timeout.
     *
     * @return int
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * Set timeout.
     *
     * $ssh->exec('ping 127.0.0.1'); on a Linux host will never return
     * and will run indefinitely. setTimeout() makes it so it'll timeout.
     * Setting $timeout to false or 0 will mean there is no timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->timeout = (int) $timeout;
        $this->connection = null;
        $this->getConnection();
    }

    /**
     * Run a command against the server (blocking).
     *
     * @param mixed $commands
     * @param int   $timeout  timeout in second
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
     * Check whether a given file exists on the server.
     *
     * @param string $remote
     *
     * @return bool
     */
    public function exists($remote) {
        return $this->getConnection()->file_exists($remote);
    }

    /**
     * Rename a remote file.
     *
     * @param string $remote
     * @param string $newRemote
     *
     * @return bool
     */
    public function rename($remote, $newRemote) {
        return $this->getConnection()->rename($remote, $newRemote);
    }

    /**
     * Delete a remote file from the server.
     *
     * @param string $remote
     *
     * @return bool
     */
    public function delete($remote) {
        return $this->getConnection()->delete($remote);
    }

    /**
     * Get the exit status of the last command.
     *
     * @return int|bool
     */
    public function status() {
        return $this->getConnection()->getExitStatus();
    }

    /**
     * Get the host used by the gateway.
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Get the port used by the gateway.
     *
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Get log ssh with defined NET_SSH2_LOGGING.
     *
     * @return string
     */
    public function getLog() {
        return $this->getConnection()->getLog();
    }

    public function disconnect() {
        if ($this->connection) {
            $this->connection->disconnect();
        }
    }

    public function __destruct() {
        //$this->disconnect();
    }
}
