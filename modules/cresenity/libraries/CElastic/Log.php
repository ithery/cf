<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 5:31:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Psr\Log\AbstractLogger;

/**
 * Elastica log object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class CElastic_Log extends AbstractLogger {

    /**
     * Log path or true if enabled.
     *
     * @var string|bool
     */
    protected $_log;

    /**
     * Last logged message.
     *
     * @var string Last logged message
     */
    protected $_lastMessage;

    /**
     * Inits log object.
     *
     * @param string|bool String to set a specific file for logging
     */
    public function __construct($log = '') {
        $this->setLog($log);
    }

    /**
     * Log a message.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null|void
     */
    public function log($level, $message, array $context = []) {
        $context['error_message'] = $message;
        $this->_lastMessage = CHelper::json()->stringify($context);
        if (!empty($this->_log) && is_string($this->_log)) {
            error_log($this->_lastMessage . PHP_EOL, 3, $this->_log);
        } else {
            error_log($this->_lastMessage);
        }
    }

    /**
     * Enable/disable log or set log path.
     *
     * @param bool|string $log Enables log or sets log path
     *
     * @return $this
     */
    public function setLog($log) {
        $this->_log = $log;
        return $this;
    }

    /**
     * Return last logged message.
     *
     * @return string Last logged message
     */
    public function getLastMessage() {
        return $this->_lastMessage;
    }

}
