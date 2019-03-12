<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:23:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ServiceAbstract implements CDaemon_ServiceInterface {

    protected $serviceName;
    protected $config;
    private $stdin;
    private $stdout;
    private $stderr;
    private $pidfile = 'pid';
    private $logFile = 'log';
    private $errFile = 'log.err';
    private $termLimit = 20;
    private $fh;
    private $childPid;
    private $didTick = false;
    private $userId = null;
    // make output pretty
    private $chars = 0;

    public function showHelp() {
        $cmd = $_SERVER['PHP_SELF'] . ' cresenity/daemon ' . CF::domain() . ' service_class=' . static::class . '&service_name=' . $this->serviceName . '&command=';
        echo "Usage: $cmd{status|start|stop|restart|reload|kill}\n";
        exit(0);
    }

    public function status() {
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
        $this->pidFile = carr::get($config, 'pidFile');
    }

    private function kill() {
        $this->terminate('Sending SIGKILL', SIGKILL);
        exit;
    }

    private function getChildPid() {
        return file_exists($this->pidFile) ? file_get_contents($this->pidFile) : false;
    }

    public function didTick() {
        $this->didTick = true;
    }

    public function start() {
        $this->show("Starting...");
        // Open and lock PID file
        $this->fh = fopen($this->pidfile, 'c+');
        if (!flock($this->fh, LOCK_EX | LOCK_NB)) {
            $this->crash("Could not lock the pidfile. This daemon may already " .
                    "be running.");
        }
        // Fork
        $this->debug("About to fork");
        $pid = pcntl_fork();
        switch ($pid) {
            case -1: // fork failed
                $this->crash("Could not fork");
                break;
            case 0: // i'm the child
                $this->childPid = getmypid();
                $this->debug("Forked - child process ($this->childPid)");
                break;
            default: // i'm the parent
                $me = getmypid();
                $this->debug("Forked - parent process ($me -> $pid)");
                fseek($this->fh, 0);
                ftruncate($this->fh, 0);
                fwrite($this->fh, $pid);
                fflush($this->fh);
                $this->debug("Parent wrote PID");
        }
        // detatch from terminal
        if (posix_setsid() === -1) {
            $this->crash("Child process could not detach from terminal.");
        }
        if (null !== $this->userId) {
            if (!posix_setuid($this->userId)) {
                $this->crash("Could not change user. Try running this program" .
                        " as root.");
            }
        }
        $this->ok();
        // stdin/etc reset
        $this->debug("Resetting file descriptors");
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        $this->stdin = fopen('/dev/null', 'r');
        $this->stdout = fopen($this->logFile, 'a+');
        $this->stderr = fopen($this->errFile, 'a+');
        $this->debug("Reopened file descriptors");
        $this->debug("Executing original script");
        $this->run();
        pcntl_signal(SIGTERM, function() {
            exit;
        });
    }

    private function terminate($msg, $signal) {
        $this->show($msg);
        $pid = $this->getChildPid();
        if (false === $pid) {
            $this->failed();
            echo "No PID file found\n";
            return;
        }
        if (!posix_kill($pid, $signal)) {
            $this->failed();
            echo "Process $pid not running!\n";
            return;
        }
        $i = 0;
        while (posix_kill($pid, 0)) { // Wait until the child goes away
            if (++$i >= $this->termLimit) {
                $this->crash("Process $pid did not terminate after $i seconds");
            }
            $this->show('.');
            sleep(1);
        }
        $this->ok();
    }

    public function __destruct() {
        if (getmypid() == $this->childPid) {
            unlink($this->pidFile);
        }
    }

    public function stop($exit = true) {
        $this->terminate('Stopping', SIGTERM);
        $exit && exit;
    }

    public function restart() {
        $this->stop(false);
        $this->start();
    }

    private function reload() {
        $pid = $this->getChildPid();
        $this->show("Sending SIGUSR1");
        if ($pid && posix_kill($pid, SIGUSR1)) {
            $this->ok();
        } else {
            $this->failed();
        }
        exit;
    }

    private function show($text) {
        echo $text;
        $this->chars += strlen($text);
    }

    private function ok() {
        echo str_repeat(' ', 59 - $this->chars);
        echo "[\033[0;32m  OK  \033[0m]\n";
        $this->chars = 0;
    }

    private function failed() {
        echo str_repeat(' ', 59 - $this->chars);
        echo "[\033[0;31mFAILED\033[0m]\n";
        $this->chars = 0;
    }

    protected function crash($msg) {
        echo $msg;
        $this->failed();
        die();
    }

    private function debug($msg) {
        echo $msg,"\n";
    }

}
