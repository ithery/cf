<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:23:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ServiceAbstract implements CDaemon_ServiceInterface {

    /**
     * Handle for log() method,
     * @see static::log()
     * @see static::restart();
     * @var stream
     */
    private static $log_handle = false;

    public function logFile() {
        return carr::get($this->config, 'logFile');
    }

    /**
     * Log the $message to the filename returned by static::logFile() and/or optionally print to stdout.
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
        static $logFile_check_at = 0;
        static $logFile_error = false;
        $header = "\nDate                  PID   Label         Message\n";
        $date = date("Y-m-d H:i:s");
        $pid = str_pad($this->pid, 5, " ", STR_PAD_LEFT);
        $label = str_pad(substr($label, 0, 12), 13, " ", STR_PAD_RIGHT);
        $prefix = "[$date] $pid $label" . str_repeat("\t", $indent);
        if (time() >= $logFile_check_at && $this->logFile() != $logFile) {
            $logFile = $this->logFile();
            $logFile_check_at = mktime(date('H'), (date('i') - (date('i') % 5)) + 5, null);
            @fclose(self::$log_handle);
            self::$log_handle = $logFile_error = false;
        }
        if (self::$log_handle === false) {
            if (strlen($logFile) > 0 && self::$log_handle = @fopen($logFile, 'a+')) {
                if ($this->parent) {
                    fwrite(self::$log_handle, $header);
                    if ($this->stdout)
                        echo $header;
                }
            } elseif (!$logFile_error) {
                $logFile_error = true;
                trigger_error(__CLASS__ . "Error: Could not write to logfile " . $logFile, E_USER_WARNING);
            }
        }
        $message = $prefix . ' ' . str_replace("\n", "\n$prefix ", trim($message)) . "\n";
        if (self::$log_handle) {
            fwrite(self::$log_handle, $message);
        }
        if ($this->stdout) {
            echo $message;
        }
    }

}
