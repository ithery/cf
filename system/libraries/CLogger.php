<?php

/**
 *
 * @author Raymond Sugiarto
 * @since  Dec 3, 2014
 * @license http://piposystem.com Piposystem
 */
class CLogger {

    const __EXT = ".log";
    // Log message levels - Windows users see PHP Bug #18090
    const EMERGENCY = LOG_EMERG;    // 0
    const ALERT = LOG_ALERT;    // 1
    const CRITICAL = LOG_CRIT;     // 2
    const ERROR = LOG_ERR;      // 3
    const WARNING = LOG_WARNING;  // 4
    const NOTICE = LOG_NOTICE;   // 5
    const INFO = LOG_INFO;     // 6
    const DEBUG = LOG_DEBUG;    // 7

    private $logLevels = [
        'emergency' => self::EMERGENCY,
        'alert' => self::ALERT,
        'critical' => self::CRITICAL,
        'error' => self::ERROR,
        'warning' => self::WARNING,
        'notice' => self::NOTICE,
        'info' => self::INFO,
        'debug' => self::DEBUG,
    ];

    /**
     * @var  CLogger  Singleton instance container
     */
    private static $_instance = NULL;

    /**
     * @var  array  list of added messages
     */
    protected $_messages = array();
    private $_group = '';
    protected static $writeOnAdd = false;

    /**
     * 
     * @return CLogger
     */
    public static function instance() {
        if (self::$_instance == NULL) {
            self::$_instance = new CLogger();
            register_shutdown_function(array(CLogger::$_instance, 'write'));
        }
        return self::$_instance;
    }

    private function __construct() {
        $options['path'] = 'system';
        $this->createWriter('file', $options);
    }

    /**
     * Create a log writer, and optionally limits the levels of messages that
     * will be written by the writer.
     *
     *     $log->create_write('file');
     *
     * @param   type        $type       string
     * @param   options     $options    array of options for writers
     * @return  CLogger
     */
    public function createWriter($type = 'file', $options = array()) {
        $levels = carr::get($options, 'levels', array());
        $min_level = carr::get($options, 'min_level', 0);
        if (!is_array($levels)) {
            $levels = range($min_level, $levels);
        }

        $writer = CLogger_Writer::factory($type, $options);
        $this->_writers["{$writer}"] = array
            (
            'object' => $writer,
            'levels' => $levels,
        );
        return $this;
    }

    /**
     * Adds a message to the log. Replacement values must be passed in to be
     * replaced using [strtr](http://php.net/strtr).
     *
     *     $log->add(Log::ERROR, 'Could not locate user: :user', array(
     *         ':user' => $username,
     *     ));
     *
     * @param   string      $level       level of message
     * @param   string      $message     message body
     * @param   array       $values      values to replace in the message
     * @param   array       $context     additional custom parameters to supply to the log writer
     * @param   Exception   $exception     Exception for log
     * @return  Log
     */
    public function add($level, $message, array $values = NULL, array $context = [], $exception = NULL) {
        if (!is_string($level)) {
            $level = carr::get(self::$logLevels, $level);
        }

        if (!is_numeric($level)) {
            $level = static::EMERGENCY;
        }

        if ($values) {
            // Insert the values into the message
            $message = strtr($message, $values);
        }

        if (strlen($message) == 0 && $exception != null) {
            $message = get_class($exception);
        }

        $trace = [];
        // Grab a copy of the trace
        if ($exception != null) {
            $trace = $exception->getTrace();
        } else {
            // Older php version don't have 'DEBUG_BACKTRACE_IGNORE_ARGS', so manually remove the args from the backtrace
            if (!defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
                $trace = array_map(function ($item) {
                    unset($item['args']);
                    return $item;
                }, array_slice(debug_backtrace(FALSE), 1));
            } else {
                $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1);
            }
        }



        // Create a new message
        $this->_messages[] = array(
            'time' => time(),
            'level' => $level,
            'body' => $message,
            'trace' => $trace,
            'domain' => CF::domain(),
            'file' => isset($trace[0]['file']) ? $trace[0]['file'] : NULL,
            'line' => isset($trace[0]['line']) ? $trace[0]['line'] : NULL,
            'class' => isset($trace[0]['class']) ? $trace[0]['class'] : NULL,
            'function' => isset($trace[0]['function']) ? $trace[0]['function'] : NULL,
            'context' => $context,
            'exception' => $exception,
        );

        if (CLogger::$writeOnAdd) {
            // Write logs as they are added
            $this->write();
        }

        return $this;
    }

    /**
     * Write and clear all of the messages.
     *
     *     $log->write();
     *
     * @return  void
     */
    public function write() {

        if (empty($this->_messages)) {
            // There is nothing to write, move along
            return;
        }

        // Import all messages locally
        $messages = $this->_messages;

        // Reset the messages array
        $this->_messages = array();

        foreach ($this->_writers as $writer) {
            if (empty($writer['levels'])) {
                // Write all of the messages
                $writer['object']->write($messages);
            } else {
                // Filtered messages
                $filtered = array();

                foreach ($messages as $message) {
                    if (in_array($message['level'], $writer['levels'])) {
                        // Writer accepts this kind of message
                        $filtered[] = $message;
                    }
                }

                // Write the filtered messages
                $writer['object']->write($filtered);
            }
        }
    }

}
