<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 3:13:20 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 *
 * A good error handling strategy is important.
 * 1. We want a daemon to be very resilient and hard to fail fatally, but when it does fail, we need it to fail loudly. Silent
 * failures are my biggest fear.
 *
 * 2. Error handlers are implemented as close to line 1 of your app as possible.
 *
 * 3. We use all the tools PHP gives us: an error handler, an exception handler, and a global shutdown handler.
 *
 */
class CDaemon_ErrorHandler {

    public static function init() {
        error_reporting(E_ALL);
        set_error_handler(array(static::class, 'daemonError'));
        set_exception_handler(array(static::class, 'daemonException'));
        register_shutdown_function(array(static::class, 'daemonShutdownFunction'));
    }

    /**
     * Override the PHP error handler while still respecting the error_reporting, display_errors and log_errors ini settings
     *
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $e Used when this is a user-generated error from an uncaught exception
     * @return boolean
     */
    public static function daemonError($errNo, $errStr, $errFile, $errLine, $errContext = null, Exception $e = null) {
        static $runonce = true;
        static $is_writable = true;


        $runningService = CDaemon::getRunningService();

        // Respect the error_reporting Level
        if (($errNo & error_reporting()) == 0)
            return true;

        $is_fatal = false;
        switch ($errNo) {
            case -1:
                // Custom - Works with the daemon_exception exception handler
                $is_fatal = true;
                $errors = 'Exception';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $errors = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $errors = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $is_fatal = true;
                $errors = 'Fatal Error';
                break;
            default:
                $errors = 'Unknown';
                break;
        }
        $message = sprintf('PHP %s: %s in %s on line %d pid %s', $errors, $errStr, $errFile, $errLine, getmypid());

        $runningService->log($message);
        if ($is_fatal) {
            if (!$e) {
                $e = new Exception;
            }
            $runningService->log(str_replace(PHP_EOL, PHP_EOL . str_repeat(' ', 23), print_r($e->getTraceAsString(), true)));
        }


        if ($is_fatal) {
            exit(1);
        }
        return true;
    }

    /**
     * Capture any uncaught exceptions and pass them as input to the error handler
     * @param Exception $e
     *
     */
    public static function daemonException(Exception $e) {
        self::daemonError(-1, $e->getMessage(), $e->getFile(), $e->getLine(), null, $e);
    }

    /**
     * When the process exits, check to make sure it wasn't caused by an un-handled error.
     * This will help us catch nearly all types of php errors.
     * @return void
     */
    public static function daemonShutdownFunction() {
        $error = error_get_last();
        if (!is_array($error) || !isset($error['type'])) {
            return;
        }
        switch ($error['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_ERROR:
                self::daemonError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

}
