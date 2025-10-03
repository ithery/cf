<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A good error handling strategy is important.
 * 1. We want a daemon to be very resilient and hard to fail fatally, but when it does fail, we need it to fail loudly. Silent
 * failures are my biggest fear.
 *
 * 2. Error handlers are implemented as close to line 1 of your app as possible.
 *
 * 3. We use all the tools PHP gives us: an error handler, an exception handler, and a global shutdown handler.
 */
class CDaemon_ErrorHandler {
    public static function init() {
        // error_reporting(E_ALL & ~E_DEPRECATED);
        set_error_handler([static::class, 'daemonError']);
        set_exception_handler([static::class, 'daemonException']);
        register_shutdown_function([static::class, 'daemonShutdownFunction']);
    }

    /**
     * Override the PHP error handler while still respecting the error_reporting, display_errors and log_errors ini settings.
     *
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $e Used when this is a user-generated error from an uncaught exception
     * @param null|mixed $errContext
     *
     * @return bool
     */
    public static function daemonError($errNo, $errStr, $errFile, $errLine, $errContext = null, Exception $e = null) {
        $runningService = CDaemon::getRunningService();

        // Respect the error_reporting Level
        if (($errNo & error_reporting()) == 0) {
            return true;
        }

        $isFatal = false;
        switch ($errNo) {
            case -1:
                // Custom - Works with the daemon_exception exception handler
                $isFatal = true;
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
                $isFatal = true;
                $errors = 'Fatal Error';

                break;
            default:
                $errors = 'Unknown';

                break;
        }

        $message = sprintf('PHP %s: %s in %s on line %d pid %s', $errors, $errStr, $errFile, $errLine, getmypid());
        $runningService->log($message);
        if ($isFatal) {
            if (!$e) {
                $e = new Exception();
            }
            $runningService->log(str_replace(PHP_EOL, PHP_EOL . str_repeat(' ', 23), print_r($e->getTraceAsString(), true)));
        }

        if (!$runningService->isDaemonContinueOnFatalError()) {
            if ($isFatal) {
                $runningService->log('Fatal Error Occured, Stopping Daemon...');
                exit(1);
            }
        }

        return true;
    }

    /**
     * Capture any uncaught exceptions and pass them as input to the error handler.
     *
     * @param Exception $e
     */
    public static function daemonException($e) {
        if ($e instanceof Exception) {
            return self::daemonError(-1, $e->getMessage(), $e->getFile(), $e->getLine(), null, $e);
        }
        if ($e instanceof \Error) {
            /**
             * @var Error $e
             */
            return self::daemonError(-1, $e->getMessage(), $e->getFile(), $e->getLine(), null, null);
        }

        return false;
    }

    /**
     * When the process exits, check to make sure it wasn't caused by an un-handled error.
     * This will help us catch nearly all types of php errors.
     *
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
