<?php

class CConsole_Prompt_Themes_Default_TableRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    /**
     * Render the table.
     *
     * @param CConsole_Prompt_Table $table
     *
     * @return $this
     */
    public function __invoke($table) {
        $tableStyle = (new \Symfony\Component\Console\Helper\TableStyle())
            ->setHorizontalBorderChar('─')
            ->setVerticalBorderChar('│')
            ->setCrossingChar('┼')
            ->setCellHeaderFormat($this->dim('<fg=default>%s</>'))
            ->setCellRowFormat('<fg=default>%s</>');

        $buffered = new CConsole_Prompt_Output_BufferedConsoleOutput();

        (new \Symfony\Component\Console\Helper\Table($buffered))
            ->setHeaders($table->headers)
            ->setRows($table->rows)
            ->setStyle($tableStyle)
            ->render();

        foreach (explode(PHP_EOL, trim($buffered->content(), PHP_EOL)) as $line) {
            $this->line(' ' . $line);
        }

        return $this;
    }
}
