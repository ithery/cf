<?php

defined('SYSPATH') or die('No direct access allowed.');

class CException extends Exception {
    /**
     * @var array PHP error code => human readable name
     */
    public static $php_errors = [
        E_ERROR => 'Fatal Error',
        E_USER_ERROR => 'User Error',
        E_PARSE => 'Parse Error',
        E_WARNING => 'Warning',
        E_USER_WARNING => 'User Warning',
        E_STRICT => 'Strict',
        E_NOTICE => 'Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
    ];

    protected static $exceptionHandler;

    /**
     * Creates a new translated exception.
     *
     *     throw new CException('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param string     $message   error message
     * @param array      $variables translation variables
     * @param int|string $code      the exception code
     * @param Exception  $previous  Previous exception
     *
     * @return void
     */
    public function __construct($message = '', array $variables = null, $code = 0, Exception $previous = null) {
        if (is_array($variables)) {
            $message = strtr($message, $variables);
        } else {
            if ($code instanceof Exception) {
                $previous = $code;
                $code = $variables;
            }
        }

        parent::__construct($message, (int) $code, $previous);
    }

    /**
     * Get a single line of text representing the exception:.
     *
     * Error [ Code ]: Message ~ File [ Line ]
     *
     * @param Exception $e
     *
     * @return string
     */
    public static function text(Exception $e) {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), cdbg::path($e->getFile()), $e->getLine());
    }

    /**
     * Magic object-to-string method.
     *
     *     echo $exception;
     *
     * @uses    CException::text
     *
     * @return string
     */
    public function __toString() {
        return self::text($this);
    }

    /**
     * Sends an Internal Server Error header.
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    public function send_headers() {
        // @codingStandardsIgnoreEnd
        // Send the 500 header
        header('HTTP/1.1 500 Internal Server Error');
    }

    /**
     * @return \CException_ExceptionHandler
     */
    public static function exceptionHandler() {
        if (static::$exceptionHandler == null) {
            static::$exceptionHandler = new CException_ExceptionHandler();
        }

        return static::$exceptionHandler;
    }

    /**
     * Create Exception Solution.
     *
     * @param string $title
     *
     * @return CException_Solution
     */
    public static function createSolution($title = '') {
        return CException_Solution::create($title);
    }

    public static function config() {
        return CException_Config::instance();
    }

    public static function manager() {
        return CException_Manager::instance();
    }

    public static function init() {
        //load all singleton for make sure exception handler can run
        static::exceptionHandler();
        static::config();
        static::manager();
    }
}
