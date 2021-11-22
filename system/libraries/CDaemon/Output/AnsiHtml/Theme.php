<?php
/**
 * Base theme.
 */
class CDaemon_Output_AnsiHtml_Theme {
    public function asCss($prefix = 'ansi_color') {
        $css = [];
        foreach ($this->asArray() as $name => $color) {
            $css[] = sprintf('.%s_fg_%s { color: %s }', $prefix, $name, $color);
            $css[] = sprintf('.%s_bg_%s { background-color: %s }', $prefix, $name, $color);
        }

        return implode("\n", $css);
    }

    public function asArray() {
        return [
            'black' => 'black',
            'red' => 'darkred',
            'green' => 'green',
            'yellow' => 'yellow',
            'blue' => 'blue',
            'magenta' => 'darkmagenta',
            'cyan' => 'cyan',
            'white' => 'white',

            'brblack' => 'black',
            'brred' => 'red',
            'brgreen' => 'lightgreen',
            'bryellow' => 'lightyellow',
            'brblue' => 'lightblue',
            'brmagenta' => 'magenta',
            'brcyan' => 'lightcyan',
            'brwhite' => 'white',
        ];
    }
}
