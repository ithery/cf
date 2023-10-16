<?php

class CML_Rubix_Logger extends \Rubix\ML\Loggers\Logger {
    /**
     * The channel name that appears on each line.
     *
     * @var string
     */
    protected string $channel;

    /**
     * The format of the timestamp.
     *
     * @var string
     */
    protected string $timestampFormat;

    protected $logs = [];

    /**
     * @param string $channel
     * @param string $timestampFormat
     */
    public function __construct(string $channel = '', string $timestampFormat = 'Y-m-d H:i:s') {
        $this->channel = trim($channel);
        $this->timestampFormat = $timestampFormat;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level
     * @param string  $message
     * @param mixed[] $context
     */
    public function log($level, $message, array $context = []) : void {
        $prefix = '';

        if ($this->timestampFormat) {
            $prefix .= '[' . date($this->timestampFormat) . '] ';
        }

        if ($this->channel) {
            $prefix .= $this->channel . '.';
        }

        $prefix .= strtoupper((string) $level);

        $this->logs[] = $prefix . ': ' . trim($message);
    }
}
