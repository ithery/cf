<?php

class CPrinter_EscPos_Renderer_HtmlRenderer extends CPrinter_EscPos_RendererAbstract {
    /**
     * @return string
     */
    public function render() {
        $data = $this->data;
        //replace initialize
        $data = str_replace(CPrinter_EscPos::ESC . '@', '', $data);

        //replace emphasis
        //$this->addCode(CPrinter_EscPos::ESC . 'E' . ($on ? chr(1) : chr(0)));
        $openEmphasis = CPrinter_EscPos::ESC . 'E' . chr(1);
        $closeEmphasis = CPrinter_EscPos::ESC . 'E' . chr(0);
        $regexEmphasis = '#' . $openEmphasis . '.+?' . $closeEmphasis . '#ims';
        $regexEmphasis = str_replace(chr(0), '\x00', $regexEmphasis);
        $data = preg_replace_callback($regexEmphasis, function ($matches) {
            $match = carr::get($matches, 0);
            $match = $this->replaceWithSpan($match, CPrinter_EscPos::ESC . 'E' . chr(1), CPrinter_EscPos::ESC . 'E' . chr(0), 'font-weight:bold');

            return $match;
        }, $data);

        //replace mode
        $allModes = CPrinter_EscPos::MODE_FONT_B | CPrinter_EscPos::MODE_EMPHASIZED | CPrinter_EscPos::MODE_DOUBLE_HEIGHT | CPrinter_EscPos::MODE_DOUBLE_WIDTH | CPrinter_EscPos::MODE_UNDERLINE;

        return $data;
    }

    private function replaceWithSpan($string, $open, $close, $style = '') {
        $spanOpen = '<span';
        if ($style) {
            $spanOpen .= ' style="' . $style . '"';
        }
        $spanOpen .= '>';
        $spanClose = '</span>';
        $string = str_replace($open, $spanOpen, $string);
        $string = str_replace($close, $spanClose, $string);

        return $string;
    }
}
