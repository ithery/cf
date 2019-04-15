<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:23:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ServiceAbstract {

    /**
     *
     * @var string
     */
    protected $serviceName;

    /**
     *
     * @var array
     */
    protected $config;

    /**
     *
     * @var string
     */
    protected $pidFile = null;

    /**
     *
     * @var bool
     */
    protected $stdout = true;

    /**
     * Handle for log() method
     * @var stream
     */
    private $logHandle = false;

    /**
     * Process ID
     * @var integer
     */
    private $pid;

    /**
     * Is Parent Process?
     * @var bool
     */
    private $parent = true;

    /**
     * startTime
     * @var bool
     */
    private $startTime = true;

    public function __construct($serviceName, $config) {
        CDaemon_ErrorHandler::init();
        $this->serviceName = $serviceName;
        $this->config = $config;
        //$this->stdout = carr::get($config, 'stdout', false);
        $this->pidFile = $this->getConfig('pidFile');
        //$this->getopt();
    }

    public function getConfig($key) {
        return carr::get($this->config, $key);
    }

    public function logFile() {
        return carr::get($this->config, 'logFile');
    }

    /**
     * Log the $message to the filename returned by CDaemon_ServiceAbstract::logFile() and/or optionally print to stdout.
     * Multi-Line messages will be handled nicely.
     *
     * Note: Your logFile() method will be called every 5 minutes (at even increments, eg 00:05, 00:10, 00:15, etc) to
     * allow you to rotate the filename based on time (one log file per month, day, hour, whatever) if you wish.
     *
     * Note: You may find value in overloading this method in your app in favor of a more fully-featured logging tool
     * like log4php or Zend_Log. There are fantastic logging libraries available, and this simplistic home-grown option
     * was chosen specifically to avoid forcing another dependency on you.
     *
     * @param string $message
     * @param string $label Truncated at 12 chars
     */
    public function log($message, $label = '', $indent = 0) {
        static $logFile = '';
        static $logFileCheckAt = 0;
        static $logFileError = false;
        $header = "\nDate                  PID   Label         Message\n";
        $date = date("Y-m-d H:i:s");
        $pid = str_pad($this->pid, 5, " ", STR_PAD_LEFT);
        $label = str_pad(substr($label, 0, 12), 13, " ", STR_PAD_RIGHT);
        $prefix = "[$date] $pid $label" . str_repeat("\t", $indent);
        if (time() >= $logFileCheckAt && $this->logFile() != $logFile) {
            $logFile = $this->logFile();
            $logFileCheckAt = mktime(date('H'), (date('i') - (date('i') % 5)) + 5, null);
            @fclose($this->logHandle);
            $this->logHandle = $logFileError = false;
        }
        if ($this->logHandle === false) {
            if (strlen($logFile) > 0 && $this->logHandle = @fopen($logFile, 'a+')) {
                if ($this->parent) {
                    fwrite($this->logHandle, $header);
                    if ($this->stdout) {
                        echo $header;
                    }
                }
            } elseif (!$logFileError) {
                $logFileError = true;
                trigger_error(__CLASS__ . "Error: Could not write to logfile " . $logFile, E_USER_WARNING);
            }
        }
        $message = $prefix . ' ' . str_replace("\n", "\n$prefix ", trim($message)) . "\n";
        if ($this->logHandle) {
            fwrite($this->logHandle, $message);
        }
        if ($this->stdout) {
            echo $message;
        }
    }

    public function start() {
        $this->startTime = time();
        try {
            $this->setPid(getmypid());
            if (pcntl_fork() > 0) {
                exit();
            }
            $this->setPid(getmypid()); // We have a new pid now
            $pidFile = $this->pidFile;

            $handle = @fopen($pidFile, 'w');
            try {
                if (!$handle) {
                    throw new Exception('Unable to write PID to ' . $this->pidFile);
                }
                fwrite($handle, $this->pid);
            } catch (Exception $ex) {
                throw $ex;
            } finally {
                fclose($handle);
            }
        } catch (Exception $e) {
            $this->fatalError($e->getMessage());
        }
        try {
            $this->log('Run EventLoop');
            $loop = CDaemon_EventLoop::createReactEventLoop();
            $loop->run();
        } catch (Exception $e) {
            $this->fatalError(sprintf('Uncaught Exception in Event Loop: %s [file] %s [line] %s%s%s', $e->getMessage(), $e->getFile(), $e->getLine(), PHP_EOL, $e->getTraceAsString()));
        }
    }

    /**
     * Combination getter/setter for the $pid property.
     * @param boolean $setValue
     * @return int
     */
    protected function setPid($pid) {
        if (!is_integer($pid)) {
            throw new Exception(__METHOD__ . ' Failed. Could not set pid. Integer Expected. Given: ' . $pid);
        }
        $this->pid = $pid;
    }

    public function getPid() {
        return $this->pid;
    }

    /**
     * Raise a fatal error and kill-off the process. If it's been running for a while, it'll try to restart itself.
     * @param string $message
     * @param string $label
     */
    public function fatalError($message, $label = '') {
        $this->error($message, $label);
        if ($this->parent) {
            $this->log(get_class($this) . ' is Shutting Down...');
            $delay = 2;
            if (($this->runtime() + $delay) > self::MIN_RESTART_SECONDS) {
                sleep($delay);
                $this->restart();
            }
        }
        // If we get here, it means we couldn't try a re-start or we tried and it just didn't work.
        echo PHP_EOL;
        exit(1);
    }

    /**
     * Log the provided $message and dispatch an ON_ERROR event.
     *
     * The library has no concept of a runtime error. If your application doesn't attach any ON_ERROR listeners, there
     * is literally no difference between using this and just passing the message to CDaemon_ServiceAbstract::log().
     *
     * @param $message
     * @param string $label
     */
    public function error($message, $label = '') {
        $this->log($message, $label);
    }

}
