<?php

abstract class CLogger_Writer {

    /**
     * @var  string  timestamp format for log entries.
     *
     * Defaults to Date::$timestamp_format
     */
    public static $timestamp;

    /**
     * @var  string  timezone for log entries
     *
     * Defaults to Date::$timezone, which defaults to date_default_timezone_get()
     */
    public static $timezone;

    /**
     * Numeric log level to string lookup table.
     * @var array
     */
    protected $_log_levels = array(
        LOG_EMERG => 'EMERGENCY',
        LOG_ALERT => 'ALERT',
        LOG_CRIT => 'CRITICAL',
        LOG_ERR => 'ERROR',
        LOG_WARNING => 'WARNING',
        LOG_NOTICE => 'NOTICE',
        LOG_INFO => 'INFO',
        LOG_DEBUG => 'DEBUG',
    );

    /**
     * @var  int  Level to use for stack traces
     */
    public static $strace_level = LOG_DEBUG;

    public static function factory($type = 'file', $options) {
        switch ($type) {
            case 'file':
                return new CLogger_Writer_File($options);
                break;
        }
        return new CLogger_Writer_File($options);
    }

    /**
     * Write an array of messages.
     *
     *     $writer->write($messages);
     *
     * @param   array   $messages
     * @return  void
     */
    abstract public function write(array $messages);

    /**
     * Allows the writer to have a unique key when stored.
     *
     *     echo $writer;
     *
     * @return  string
     */
    final public function __toString() {
        return spl_object_hash($this);
    }

    /**
     * Formats a log entry.
     *
     * @param   array   $message
     * @param   string  $format
     * @return  string
     */
    public function format_message(array $message, $format = "time --- domain:level: body in file:line") {
        $message['time'] = date('Y-m-d H:i:s', $message['time']);
        $message['level'] = $this->_log_levels[$message['level']];

        $string = strtr($format, array_filter($message, 'is_scalar'));

        if (isset($message['exception']) && $message['exception'] != null) {
            // Re-use as much as possible, just resetting the body to the trace

            if (carr::get($message, 'level') >= static::$strace_level) {
                $message['body'] .= $message['trace'];
            }
            if(isset($this->_log_levels[$message['level']])) {
                $message['level'] = $this->_log_levels[$message['level']];
            }

            $string .= PHP_EOL . strtr($format, array_filter($message, 'is_scalar'));
        }

        return $string;
    }

}
