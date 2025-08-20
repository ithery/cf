<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class CRemote_SSH {
    protected $name;

    protected $config;

    /**
     * @var CRemote_SSH_Connection
     */
    protected $connection;

    /**
     * Get a remote connection instance.
     *
     * @param string|array|mixed $name
     *
     * @return CRemote_SSH_ConnectionInterface
     */
    public function __construct(array $config) {
        $this->config = $config;

        $this->name = carr::get($config, 'name', carr::get($config, 'host'));
        $this->connection = $this->makeConnection($this->name, $config);
    }

    /**
     * Get a remote connection instance.
     *
     * @return CRemote_SSH_Connection
     */
    public function connection() {
        return $this->connection;
    }

    /**
     * Make a new connection instance.
     *
     * @param string $name
     * @param array  $config
     *
     * @return CRemote_SSH_Connection
     */
    protected function makeConnection($name, array $config) {
        $timeout = isset($config['timeout']) ? $config['timeout'] : 10;
        $host = carr::get($config, 'ip_address');
        if ($host == null) {
            $host = carr::get($config, 'host');
        }

        $this->setOutput($connection = new CRemote_SSH_Connection(
            $name,
            $host,
            carr::get($config, 'port', 22),
            $config['username'],
            $this->getAuth($config),
            null,
            $timeout
        ));

        return $connection;
    }

    /**
     * Set the output implementation on the connection.
     *
     * @param CRemote_SSH_Connection $connection
     *
     * @return void
     */
    protected function setOutput(CRemote_SSH_Connection $connection) {
        $output = php_sapi_name() == 'cli' ? new ConsoleOutput() : new BufferedOutput();

        $connection->setOutput($output);
    }

    /**
     * Format the appropriate authentication array payload.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getAuth(array $config) {
        if (isset($config['agent']) && $config['agent'] === true) {
            return ['agent' => true];
        } elseif (isset($config['key']) && trim($config['key']) != '') {
            return ['key' => $config['key'], 'keyphrase' => $config['keyphrase']];
        } elseif (isset($config['keytext']) && trim($config['keytext']) != '') {
            return ['keytext' => $config['keytext']];
        } elseif (isset($config['password'])) {
            return ['password' => $config['password']];
        }

        throw new \InvalidArgumentException('Password / key is required.');
    }

    public function output() {
        $output = $this->connection->getOutput();
        if ($output instanceof BufferedOutput) {
            return $output->fetch();
        }

        return null;
    }

    public function outputContent() {
        $output = $this->output();
        $content = preg_replace('/\[.+?\] \(*.+?\) /', '', $output);

        return $content;
    }

    /**
     * Run a set of commands to the connection.
     *
     * @param string|array $commands
     * @param \Closure     $callback
     *
     * @return $this
     */
    public function run($commands, Closure $callback = null) {
        $this->connection->run($commands, $callback);

        return $this;
    }

    /**
     * @param string $commands
     *
     * @return string
     */
    public function exec($commands) {
        return $this->connection->exec($commands);
    }

    /**
     * Run a set of commands against the connection (blocking).
     *
     * @param string|array $commands
     * @param mixed        $timeout
     *
     * @return string
     */
    public function runBlocking($commands, $timeout = 2) {
        return $this->connection->runBlocking($commands, $timeout);
    }

    /**
     * Get log ssh with defined NET_SSH2_LOGGING.
     *
     * @return string
     */
    public function getLog() {
        return $this->connection->getGateway()->getLog();
    }

    public function disconnect() {
        return $this->connection->disconnect();
    }

    public function reconnect() {
        $this->disconnect();
        $this->connection = $this->makeConnection($this->name, $this->config);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return call_user_func_array([$this->connection, $method], $parameters);
    }

    /**
     * @return \phpseclib3\Net\SFTP
     */
    public function getClient() {
        return $this->connection->getGateway()->getConnection();
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
        $this->connection->put($local, $remote);
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
        $this->connection->putString($remote, $contents);
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
        $this->connection->get($remote, $local);
    }

    /**
     * Get the contents of a remote file.
     *
     * @param string $remote
     *
     * @return string
     */
    public function getString($remote) {
        return $this->connection->getString($remote);
    }

    public function getConfig() {
        return $this->config;
    }

    public function getHost() {
        return carr::get($this->config, 'host', carr::get($this->config, 'ip_address'));
    }
}
