<?php

use Symfony\Component\Console\Output\OutputInterface;

class CConsole_View_Component_Line extends CConsole_View_ComponentAbstract {
    /**
     * The possible line styles.
     *
     * @var array<string, array<string, string>>
     */
    protected static $styles = [
        'info' => [
            'bgColor' => 'blue',
            'fgColor' => 'white',
            'title' => 'info',
        ],
        'warn' => [
            'bgColor' => 'yellow',
            'fgColor' => 'black',
            'title' => 'warn',
        ],
        'error' => [
            'bgColor' => 'red',
            'fgColor' => 'white',
            'title' => 'error',
        ],
    ];

    /**
     * Renders the component using the given arguments.
     *
     * @param string $style
     * @param string $string
     * @param int    $verbosity
     *
     * @return void
     */
    public function render($style, $string, $verbosity = OutputInterface::VERBOSITY_NORMAL) {
        $string = $this->mutate($string, [
            CConsole_View_Component_Mutator_EnsureDynamicContentIsHighlighted::class,
            CConsole_View_Component_Mutator_EnsurePunctuation::class,
            CConsole_View_Component_Mutator_EnsureRelativePaths::class,
        ]);

        $this->renderView('line', array_merge(static::$styles[$style], [
            'marginTop' => $this->output instanceof CConsole_Contract_NewLineAwareInterface ? max(0, 2 - $this->output->newLinesWritten()) : 1,
            'content' => $string,
        ]), $verbosity);
    }
}
