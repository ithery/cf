<?php

use Symfony\Component\Console\Output\OutputInterface;

class CConsole_View_Component_BulletList extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param array<int, string> $elements
     * @param int                $verbosity
     *
     * @return void
     */
    public function render($elements, $verbosity = OutputInterface::VERBOSITY_NORMAL) {
        $elements = $this->mutate($elements, [
            CConsole_View_Component_Mutator_EnsureDynamicContentIsHighlighted::class,
            CConsole_View_Component_Mutator_EnsureNoPunctuation::class,
            CConsole_View_Component_Mutator_EnsureRelativePaths::class,
        ]);

        $this->renderView('bullet-list', [
            'elements' => $elements,
        ], $verbosity);
    }
}
