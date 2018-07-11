<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 1:48:03 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class CRemote_SSH {

    protected $config;

    /**
     *
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
     * @return CRemote_SSH_ConnectionInterface
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
     * @return \Collective\Remote\Connection
     */
    protected function makeConnection($name, array $config) {
        $timeout = isset($config['timeout']) ? $config['timeout'] : 10;
        $host = carr::get($config, 'ip_address');
        if (strlen($host) == 0) {
            $host = carr::get($config, 'host');
        }
        $this->setOutput($connection = new CRemote_SSH_Connection(
                $name, $host, carr::get($config, 'port', 22), $config['username'], $this->getAuth($config), null, $timeout
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

}
