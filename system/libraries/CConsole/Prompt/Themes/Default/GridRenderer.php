<?php

class CConsole_Prompt_Themes_Default_GridRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_InteractsWithStrings;

    /**
     * @var int
     */
    protected $minWidth = 60;

    /**
     * Render the grid.
     *
     * @param CConsole_Prompt_Grid $grid
     *
     * @return string
     */
    public function __invoke($grid) {
        if (empty($grid->items)) {
            return $this;
        }

        $maxWidth = $grid->maxWidth - 2;
        $cellWidth = max(array_map(function ($item) {
            return mb_strwidth($this->stripEscapeSequences($item));
        }, $grid->items)) + 4;
        $maxColumns = max(1, (int) floor(($maxWidth - 1) / ($cellWidth + 1)));
        $columnCount = max(1, $this->balancedColumnCount(count($grid->items), $maxColumns));

        $rows = $this->buildRowsWithSeparators($grid->items, $columnCount);

        $tableStyle = (new \Symfony\Component\Console\Helper\TableStyle())
            ->setHorizontalBorderChar('─')
            ->setVerticalBorderChar('│')
            ->setCellRowFormat('<fg=default>%s</>')
            ->setCrossingChar('┼');

        $buffered = new CConsole_Prompt_Output_BufferedConsoleOutput();

        (new \Symfony\Component\Console\Helper\Table($buffered))
            ->setRows($rows)
            ->setStyle($tableStyle)
            ->render();

        foreach (explode(PHP_EOL, trim($buffered->content(), PHP_EOL)) as $line) {
            $this->line(' ' . $line);
        }

        return $this;
    }

    /**
     * Calculate a balanced column count for even row distribution.
     *
     * @param int $itemCount
     * @param int $maxColumns
     *
     * @return int
     */
    protected function balancedColumnCount($itemCount, $maxColumns) {
        if ($itemCount <= $maxColumns) {
            return $itemCount;
        }

        for ($cols = $maxColumns; $cols >= 1; $cols--) {
            $remainder = $itemCount % $cols;

            if ($remainder === 0 || $remainder >= (int) ceil($cols / 2)) {
                return $cols;
            }
        }

        return $maxColumns;
    }

    /**
     * Build rows with separators between them.
     *
     * @param array<int, string> $items
     * @param int                 $columnCount
     *
     * @return array<int, array<int, string>|\Symfony\Component\Console\Helper\TableSeparator>
     */
    protected function buildRowsWithSeparators(array $items, $columnCount) {
        $chunks = array_chunk($items, $columnCount);
        $rows = [];

        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                $rows[] = new \Symfony\Component\Console\Helper\TableSeparator();
            }

            $rows[] = array_pad($chunk, $columnCount, '');
        }

        return $rows;
    }
}
