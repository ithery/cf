<?php

class CConsole_Prompt_Output_BufferedConsoleOutput extends CConsole_Prompt_Output_ConsoleOutput {
    /**
     * The output buffer.
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * Empties the buffer and returns its content.
     *
     * @return string
     */
    public function fetch() {
        $content = $this->buffer;
        $this->buffer = '';

        return $content;
    }

    /**
     * Return the content of the buffer.
     *
     * @return string
     */
    public function content() {
        return $this->buffer;
    }

    /**
     * Write to the output buffer.
     *
     * @param string $message
     * @param bool   $newline
     *
     * @return void
     */
    protected function doWrite($message, $newline) {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= PHP_EOL;
        }
    }

    /**
     * Write output directly, bypassing newline capture.
     *
     * @param string $message
     *
     * @return void
     */
    public function writeDirectly($message) {
        $this->doWrite($message, false);
    }
}
