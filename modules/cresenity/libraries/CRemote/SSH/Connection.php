<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 2:01:51 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CRemote_SSH_Connection implements CRemote_SSH_ConnectionInterface {

    /**
     * The SSH gateway implementation.
     *
     * @var \Collective\Remote\GatewayInterface
     */
    protected $gateway;

    /**
     * The name of the connection.
     *
     * @var string
     */
    protected $name;

    /**
     * The port of the connection.
     *
     * @var int
     */
    protected $port;

    /**
     * The host name of the server.
     *
     * @var string
     */
    protected $host;

    /**
     * The username for the connection.
     *
     * @var string
     */
    protected $username;

    /**
     * All of the defined tasks.
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * The output implementation for the connection.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Create a new SSH connection instance.
     *
     * @param string                              $name
     * @param string                              $host
     * @param string                              $username
     * @param array                               $auth
     * @param CRemote_SSH_GatewayInterface        $gateway
     * @param int                                 $timeout
     */
    public function __construct($name, $host, $port, $username, array $auth, CRemote_SSH_GatewayInterface $gateway = null, $timeout = 10) {
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->gateway = $gateway ?: new CRemote_SSH_Gateway($host, $port, $auth, $timeout);
    }

    /**
     * Define a set of commands as a task.
     *
     * @param string       $task
     * @param string|array $commands
     *
     * @return void
     */
    public function define($task, $commands) {
        $this->tasks[$task] = $commands;

        return $this;
    }

    /**
     * Run a task against the connection.
     *
     * @param string   $task
     * @param \Closure $callback
     *
     * @return void
     */
    public function task($task, Closure $callback = null) {
        if (isset($this->tasks[$task])) {
            $this->run($this->tasks[$task], $callback);
        }
    }

    /**
     * Run a set of commands against the connection.
     *
     * @param string|array $commands
     * @param \Closure     $callback
     *
     * @return $this
     */
    public function run($commands, Closure $callback = null) {
        // First, we will initialize the SSH gateway, and then format the commands so
        // they can be run. Once we have the commands formatted and the server is
        // ready to go we will just fire off these commands against the server.
        $gateway = $this->getGateway();

        $callback = $this->getCallback($callback);

        $response = $gateway->run($this->formatCommands($commands), $callback);
    }

    /**
     * Run a set of commands against the connection (blocking).
     *
     * @param string|array $commands
     * @param \Closure     $callback
     *
     * @return $this
     */
    public function runBlocking($commands, $timeout = 2) {
        // First, we will initialize the SSH gateway, and then format the commands so
        // they can be run. Once we have the commands formatted and the server is
        // ready to go we will just fire off these commands against the server.
        $gateway = $this->getGateway();


        return $gateway->runBlocking($this->formatCommands($commands), $timeout);
    }

    /**
     * Get the gateway implementation.
     *
     * @throws \RuntimeException
     *
     * @return \CRemote_SSH_Gateway
     */
    public function getGateway() {
        if (!$this->gateway->connected() && !$this->gateway->connect($this->username)) {
            throw new \RuntimeException('Unable to connect to remote server.');
        }

        return $this->gateway;
    }

    /**
     * Get the display callback for the connection.
     *
     * @param \Closure|null $callback
     *
     * @return \Closure
     */
    protected function getCallback($callback) {
        if (!is_null($callback)) {
            return $callback;
        }

        return function ($line) {
            $this->display($line);
        };
    }

    /**
     * Display the given line using the default output.
     *
     * @param string $line
     *
     * @return void
     */
    public function display($line) {
        $server = $this->username . '@' . $this->host;

        $lead = '<comment>[' . $server . ']</comment> <info>(' . $this->name . ')</info>';

        $this->getOutput()->writeln($lead . ' ' . $line);
    }

    /**
     * Get the output implementation for the connection.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput() {
        if (is_null($this->output)) {
            $this->output = new BufferedOutput();
        }

        return $this->output;
    }

    /**
     * Set the output implementation.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function setOutput(OutputInterface $output) {
        $this->output = $output;
    }

    /**
     * Format the given command set.
     *
     * @param string|array $commands
     *
     * @return string
     */
    protected function formatCommands($commands) {
        return is_array($commands) ? implode(' && ', $commands) : $commands;
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
        $this->getGateway()->get($remote, $local);
    }

    /**
     * Get the contents of a remote file.
     *
     * @param string $remote
     *
     * @return string
     */
    public function getString($remote) {
        return $this->getGateway()->getString($remote);
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
        $this->getGateway()->put($local, $remote);
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
        $this->getGateway()->putString($remote, $contents);
    }

    /**
     * Check whether a given file exists on the server.
     *
     * @param string $remote
     *
     * @return bool
     */
    public function exists($remote) {
        return $this->getGateway()->exists($remote);
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
        return $this->getGateway()->rename($remote, $newRemote);
    }

    /**
     * Delete a remote file from the server.
     *
     * @param string $remote
     *
     * @return bool
     */
    public function delete($remote) {
        return $this->getGateway()->delete($remote);
    }

    /**
     * Get the exit status of the last command.
     *
     * @return int|bool
     */
    public function status() {
        return $this->gateway->status();
    }

    /**
     * Set the time out of current gateway.
     *
     * @return int|bool
     */
    public function setTimeout($second) {
        return $this->gateway->setTimeout($second);
    }

    /**
     * Get the time out of current gateway.
     *
     * @return int
     */
    public function getTimeout() {
        return $this->gateway->getTimeout();
    }

}
