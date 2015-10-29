<?php

/**
 * Simple Logging Manager.
 * This does an error_log for now
 * Potential frameworks to use are PEAR logger, log4php from Apache
 */
class PayPal_Core_PayPalLoggingManager
{

    /**
     * Default Logging Level
     */
    const DEFAULT_LOGGING_LEVEL = 0;

    /**
     * Logger Name
     * @var string
     */
    private $loggerName;

    /**
     * Log Enabled
     *
     * @var bool
     */
    private $isLoggingEnabled;

    /**
     * Configured Logging Level
     *
     * @var int|mixed
     */
    private $loggingLevel;

    /**
     * Configured Logging File
     *
     * @var string
     */
    private $loggerFile;

    /**
     * Returns the singleton object
     *
     * @param string $loggerName
     * @return $this
     */
    public static function getInstance($loggerName = __CLASS__)
    {
        $instance = new self();
        $instance->setLoggerName($loggerName);
        return $instance;
    }

    /**
     * Sets Logger Name. Generally defaulted to Logging Class
     *
     * @param string $loggerName
     */
    public function setLoggerName($loggerName = __CLASS__)
    {
        $this->loggerName = $loggerName;
    }

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $config = PayPal_Core_PayPalConfigManager::getInstance()->getConfigHashmap();

        $this->isLoggingEnabled = (array_key_exists('log.LogEnabled', $config) && $config['log.LogEnabled'] == '1');

        if ($this->isLoggingEnabled) {
            $this->loggerFile = ($config['log.FileName']) ? $config['log.FileName'] : ini_get('error_log');
            $loggingLevel = strtoupper($config['log.LogLevel']);
            $this->loggingLevel =
                (isset($loggingLevel) && defined(__NAMESPACE__ . "\\PayPalLoggingLevel::$loggingLevel")) ?
                constant(__NAMESPACE__ . "\\PayPalLoggingLevel::$loggingLevel") :
                PayPal_Core_PayPalLoggingManager::DEFAULT_LOGGING_LEVEL;
        }
    }

    /**
     * Default Logger
     *
     * @param string $message
     * @param int $level
     */
    private function log($message, $level = PayPal_Core_PayPalLoggingLevel::INFO)
    {
        if ($this->isLoggingEnabled) {
            $config = PayPal_Core_PayPalConfigManager::getInstance()->getConfigHashmap();
            // Check if logging in live
            if (array_key_exists('mode', $config) && $config['mode'] == 'live') {
                // Live should not have logging level above INFO.
                if ($this->loggingLevel >= PayPal_Core_PayPalLoggingLevel::INFO) {
                    // If it is at Debug Level, throw an warning in the log.
                    if ($this->loggingLevel == PayPal_Core_PayPalLoggingLevel::DEBUG) {
                        error_log("[" . date('d-m-Y h:i:s') . "] " . $this->loggerName . ": ERROR\t: Not allowed to keep 'Debug' level for Live Environments. Reduced to 'INFO'\n", 3, $this->loggerFile);
                    }
                    // Reducing it to info level
                    $this->loggingLevel = PayPal_Core_PayPalLoggingLevel::INFO;
                }
            }

            if ($level <= $this->loggingLevel) {
                error_log("[" . date('d-m-Y h:i:s') . "] " . $this->loggerName . ": $message\n", 3, $this->loggerFile);
            }
        }
    }

    /**
     * Log Error
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->log("ERROR\t: " . $message, PayPal_Core_PayPalLoggingLevel::ERROR);
    }

    /**
     * Log Warning
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->log("WARNING\t: " . $message, PayPal_Core_PayPalLoggingLevel::WARN);
    }

    /**
     * Log Info
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->log("INFO\t: " . $message, PayPal_Core_PayPalLoggingLevel::INFO);
    }

    /**
     * Log Fine
     *
     * @param string $message
     */
    public function fine($message)
    {
        $this->log("FINE\t: " . $message, PayPal_Core_PayPalLoggingLevel::FINE);
    }

    /**
     * Log Fine
     *
     * @param string $message
     */
    public function debug($message)
    {
        $this->log("DEBUG\t: " . $message, PayPal_Core_PayPalLoggingLevel::DEBUG);
    }

}
