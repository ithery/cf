<?php

trait CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes {
    use CConsole_Prompt_Themes_Default_Concerns_InteractsWithStrings;

    /**
     * @var int
     */
    protected $minWidth = 60;

    /**
     * Draw a box.
     *
     * @param string $title
     * @param string $body
     * @param string $footer
     * @param string $color
     * @param string $info
     *
     * @return $this
     */
    protected function box($title, $body, $footer = '', $color = 'gray', $info = '') {
        $this->minWidth = min($this->minWidth, CConsole_Prompt::terminal()->cols() - 6);

        $bodyLines = explode(PHP_EOL, $body);
        $footerLines = array_filter(explode(PHP_EOL, $footer));

        $width = $this->longest(array_merge($bodyLines, $footerLines, [$title]));

        $titleLength = mb_strwidth($this->stripEscapeSequences($title));
        $titleLabel = $titleLength > 0 ? " {$title} " : '';
        $topBorder = str_repeat('─', $width - $titleLength + ($titleLength > 0 ? 0 : 2));

        $this->line("{$this->{$color}(' ┌')}{$titleLabel}{$this->{$color}($topBorder . '┐')}");

        foreach ($bodyLines as $line) {
            $this->line("{$this->{$color}(' │')} {$this->pad($line, $width)} {$this->{$color}('│')}");
        }

        if (count($footerLines) > 0) {
            $this->line($this->{$color}(' ├' . str_repeat('─', $width + 2) . '┤'));

            foreach ($footerLines as $line) {
                $this->line("{$this->{$color}(' │')} {$this->pad($line, $width)} {$this->{$color}('│')}");
            }
        }

        if ($info) {
            $info = $this->truncate($info, $width - 1);
        }

        $this->line($this->{$color}(' └' . str_repeat(
            '─',
            $info ? ($width - mb_strwidth($this->stripEscapeSequences($info))) : ($width + 2)
        ) . ($info ? " {$info} " : '') . '┘'));

        return $this;
    }
}
