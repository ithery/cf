<?php

use Symfony\Component\Console\Output\OutputInterface;

class CConsole_View_Component_Error extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string $string
     * @param int    $verbosity
     *
     * @return void
     */
    public function render($string, $verbosity = OutputInterface::VERBOSITY_NORMAL) {
        c::with(new CConsole_View_Component_Line($this->output))->render('error', $string, $verbosity);
    }
}
