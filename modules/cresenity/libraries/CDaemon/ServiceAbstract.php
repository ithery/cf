<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:23:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ServiceAbstract {

    protected $serviceName;
    protected $config;
    protected $logFile;
    protected $didTick;
    protected $errFile;
    // make output pretty
    private static $chars = 0;

    private function status() {
        $pid = $this->getChildPid();
        if (!$pid) {
            echo "Process is stopped\n";
            exit(3);
        }
        if (posix_kill($pid, 0)) {
            echo "Process (pid $pid) is running...\n";
            exit(0);
        }
        // # See if /var/lock/subsys/${base} exists
        // if [ -f /var/lock/subsys/${base} ]; then
        // 	echo $"${base} dead but subsys locked"
        // 	return 2
        // fi¬
        else {
            echo "Process dead but pid file exists\n";
            exit(1);
        }
    }

    public function __construct($serviceName, array $config) {
        $this->serviceName = $serviceName;
        $this->config = $config + [
            'logFile' => 'log',
            'errFile' => 'log.err',
        ];
        $this->logFile = carr::get($config, 'logFile');
        $this->errFile = carr::get($config, 'errFile');
    }

    private function kill() {
        $this->terminate('Sending SIGKILL', SIGKILL);
        exit;
    }

    private function getChildPid() {
        return file_exists($this->pidfile) ? file_get_contents($this->pidfile) : false;
    }

    private static function show($text) {
        echo $text;
        self::$chars += strlen($text);
    }

    private static function ok() {
        echo str_repeat(' ', 59 - self::$chars);
        echo "[\033[0;32m  OK  \033[0m]\n";
        self::$chars = 0;
    }

    private function failed() {
        echo str_repeat(' ', 59 - self::$chars);
        echo "[\033[0;31mFAILED\033[0m]\n";
        self::$chars = 0;
    }

    protected function crash($msg) {
        $this->failed();
        die($msg);
    }

}
