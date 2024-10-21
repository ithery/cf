<?php

use Symfony\Component\Console\Output\OutputInterface;

class CConsole_View_Component_Alert extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string $string
     * @param int    $verbosity
     *
     * @return void
     */
    public function render($string, $verbosity = OutputInterface::VERBOSITY_NORMAL) {
        $string = $this->mutate($string, [
            CConsole_View_Component_Mutator_EnsureDynamicContentIsHighlighted::class,
            CConsole_View_Component_Mutator_EnsurePunctuation::class,
            CConsole_View_Component_Mutator_EnsureRelativePaths::class,
        ]);

        $this->renderView('alert', [
            'content' => $string,
        ], $verbosity);
    }
}
