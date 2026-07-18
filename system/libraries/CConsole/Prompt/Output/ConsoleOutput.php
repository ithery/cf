<?php

class CConsole_Prompt_Output_ConsoleOutput extends \Symfony\Component\Console\Output\ConsoleOutput {
    /**
     * How many new lines were written by the last output.
     *
     * @var int
     */
    protected $newLinesWritten = 1;

    /**
     * How many new lines were written by the last output.
     *
     * @return int
     */
    public function newLinesWritten() {
        return $this->newLinesWritten;
    }

    /**
     * Write the output and capture the number of trailing new lines.
     *
     * @param string $message
     * @param bool   $newline
     *
     * @return void
     */
    protected function doWrite($message, $newline) {
        parent::doWrite($message, $newline);

        if ($newline) {
            $message .= PHP_EOL;
        }

        preg_match('/(?:\r?\n)*$/', $message, $matches);

        $trailingNewLines = substr_count(isset($matches[0]) ? $matches[0] : '', "\n");

        if (trim($message) === '') {
            $this->newLinesWritten += $trailingNewLines;
        } else {
            $this->newLinesWritten = $trailingNewLines;
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
        parent::doWrite($message, false);
    }
}
