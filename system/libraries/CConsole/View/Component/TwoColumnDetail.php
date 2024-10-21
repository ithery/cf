<?php

use Symfony\Component\Console\Output\OutputInterface;

class CConsole_View_Component_TwoColumnDetail extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string      $first
     * @param null|string $second
     * @param int         $verbosity
     *
     * @return void
     */
    public function render($first, $second = null, $verbosity = OutputInterface::VERBOSITY_NORMAL) {
        $first = $this->mutate($first, [
            CConsole_View_Component_Mutator_EnsureDynamicContentIsHighlighted::class,
            CConsole_View_Component_Mutator_EnsureNoPunctuation::class,
            CConsole_View_Component_Mutator_EnsureRelativePaths::class,
        ]);

        $second = $this->mutate($second, [
            CConsole_View_Component_Mutator_EnsureDynamicContentIsHighlighted::class,
            CConsole_View_Component_Mutator_EnsureNoPunctuation::class,
            CConsole_View_Component_Mutator_EnsureRelativePaths::class,
        ]);

        $this->renderView('two-column-detail', [
            'first' => $first,
            'second' => $second,
        ], $verbosity);
    }
}
