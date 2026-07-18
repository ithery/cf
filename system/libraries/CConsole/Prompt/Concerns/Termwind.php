<?php

use function Termwind\render;
use function Termwind\renderUsing;

trait CConsole_Prompt_Concerns_Termwind {
    /**
     * @param string $html
     *
     * @return string
     */
    protected function termwind($html) {
        $output = new CConsole_Prompt_Output_BufferedConsoleOutput();
        renderUsing($output);

        render($html);

        return $this->restoreEscapeSequences($output->fetch());
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function restoreEscapeSequences($string) {
        return preg_replace('/\[(\d+)m/', "\e[" . '\1m', $string);
    }
}
