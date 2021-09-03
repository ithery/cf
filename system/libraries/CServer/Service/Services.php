<?php

use Symfony\Component\Process\Process;

class CServer_Service_Services {
    const STATUS_STOPPED = 3;

    /**
     * @var string
     */
    private static $command = 'systemctl';

    /**
     * @var bool
     */
    private static $sudo = true;

    /**
     * @var int
     */
    private static $timeout = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * Sets the systemctl command to use.
     *
     * @param string $command
     */
    public static function setCommand($command) {
        self::$command = $command;
    }

    /**
     * Specifies whether or not to use sudo to run the systemctl command.
     *
     * @param bool $flag
     */
    public static function sudo($flag = true) {
        self::$sudo = (bool) $flag;
    }

    /**
     * Specifies the timeout in seconds for the systemctl process.
     *
     * @param int $timeout
     */
    public static function setTimeout($timeout) {
        self::$timeout = (int) $timeout;
    }

    /**
     * @param string $name Name of the service to manage
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Checks whether or not the service is running.
     *
     * @throws CommandFailedException If the command failed
     *
     * @return bool
     */
    public function isRunning() {
        $process = $this->getProcess([
            '--lines=0',
            'status',
            $this->name,
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            return true;
        }
        if (self::STATUS_STOPPED === $process->getExitCode()) {
            return false;
        }

        throw new CServer_Service_Exception_CommandFailedException($process);
    }

    /**
     * Starts the service.
     *
     * @throws CServer_Service_Exception_CommandFailedException If the command failed
     */
    public function start() {
        if ($this->isRunning()) {
            return;
        }

        $process = $this->getProcess([
            'start',
            $this->name,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CServer_Service_Exception_CommandFailedException($process);
        }
    }

    /**
     * Stops the service.
     *
     * @throws CServer_Service_Exception_CommandFailedException If the command failed
     */
    public function stop() {
        if (!$this->isRunning()) {
            return;
        }

        $process = $this->getProcess([
            'stop',
            $this->name,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CServer_Service_Exception_CommandFailedException($process);
        }
    }

    /**
     * Restarts the service.
     *
     * @throws CServer_Service_Exception_CommandFailedException If the command failed
     */
    public function restart() {
        $process = $this->getProcess([
            'restart',
            $this->name,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CServer_Service_Exception_CommandFailedException($process);
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * Creates and prepares a process.
     *
     * @param string[] $arguments
     *
     * @return Process
     */
    private function getProcess(array $arguments) {
        $command = explode(' ', self::$command);
        if (self::$sudo) {
            array_unshift($command, 'sudo');
        }
        $command = array_merge($command, $arguments);

        $process = new Process($command);
        $process->setTimeout(self::$timeout);

        return $process;
    }
}
