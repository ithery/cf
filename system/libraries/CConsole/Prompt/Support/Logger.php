<?php

class CConsole_Prompt_Support_Logger {
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var null|resource
     */
    protected $socket;

    /**
     * The buffer for streaming text.
     *
     * @var string
     */
    protected $streamBuffer = '';

    /**
     * Create a new Logger instance.
     *
     * @param string        $identifier
     * @param null|resource $socket
     */
    public function __construct($identifier, $socket = null) {
        $this->identifier = $identifier;
        $this->socket = $socket;
    }

    /**
     * Log a line to the process log.
     *
     * @param string $message
     *
     * @return void
     */
    public function line($message) {
        $this->write(rtrim($message));
    }

    /**
     * Append a chunk of text, accumulating on the current line(s).
     *
     * @param string $chunk
     *
     * @return void
     */
    public function partial($chunk) {
        $this->streamBuffer .= $chunk;
        $this->write($this->streamBuffer, 'partial');
    }

    /**
     * Commit the accumulated partial text and start fresh.
     *
     * @return void
     */
    public function commitPartial() {
        $this->streamBuffer = '';
        $this->write('', 'commitpartial');
    }

    /**
     * Log a success message to the process log.
     *
     * @param string $message
     *
     * @return void
     */
    public function success($message) {
        $this->write($message, 'success');
    }

    /**
     * Log a warning message to the process log.
     *
     * @param string $message
     *
     * @return void
     */
    public function warning($message) {
        $this->write($message, 'warning');
    }

    /**
     * Log an error message to the process log.
     *
     * @param string $message
     *
     * @return void
     */
    public function error($message) {
        $this->write($message, 'error');
    }

    /**
     * Update the label of the process log.
     *
     * @param string $message
     *
     * @return void
     */
    public function label($message) {
        $this->write($message, 'label');
    }

    /**
     * Update the sub-label of the process log. Pass an empty string to clear.
     *
     * @param string $message
     *
     * @return void
     */
    public function subLabel($message) {
        $this->write($message, 'sublabel');
    }

    /**
     * Write a message to the socket.
     *
     * @param string      $message
     * @param null|string $type
     *
     * @return void
     */
    protected function write($message, $type = null) {
        if ($this->socket === null) {
            return;
        }

        if ($type !== null) {
            fwrite($this->socket, $this->prefix($type, $message) . PHP_EOL);
        } else {
            fwrite($this->socket, $message . PHP_EOL);
        }
    }

    /**
     * Prefix a message with the identifier and type.
     *
     * @param string $type
     * @param string $message
     *
     * @return string
     */
    protected function prefix($type, $message) {
        return $this->identifier . '_' . $type . ':' . rtrim($message, PHP_EOL);
    }
}
