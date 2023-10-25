<?php

interface CConsole_Contract_NewLineAwareInterface {
    /**
     * How many trailing newlines were written.
     *
     * @return int
     */
    public function newLinesWritten();

    /**
     * Whether a newline has already been written.
     *
     * @return bool
     *
     * @deprecated use newLinesWritten
     */
    public function newLineWritten();
}
