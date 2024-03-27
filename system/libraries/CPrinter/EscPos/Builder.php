<?php

class CPrinter_EscPos_Builder {
    public $data = '';

    public $width = 80;

    public $height = 30;

    public $currentX = 0;

    public $currentY = 0;

    public $currentLine = 0;

    public $currentPage = 1;

    public $header = null;

    public $footer = null;

    public $show;

    private $driver = null;

    public function __construct($driver_name = null) {
        // if ($driver_name == null) {
        //     $driver_name = 'LX300';
        // }
        // $driver_location = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'drivers' . DIRECTORY_SEPARATOR . 'CRawPrint' . DIRECTORY_SEPARATOR . $driver_name . EXT;

        // require_once $driver_location;

        // $this->driver = new $driver_name();
        $this->data = '';
    }

    public static function factory() {
        return new CPrinter_EscPos_Builder();
    }

    public function currentY() {
        return $this->currentY;
    }

    public function reset() {
        $this->currentY = 0;
        $this->data = '';
    }

    public function br() {
        $this->data .= "\n";
        $this->currentY++;

        return $this;
    }

    public function ff() {
        $this->data .= chr(12);

        return $this;
    }

    public function escapeCode($code) {
        $this->data .= chr(27) . $code;

        return $this;
    }

    public function addCode($codex) {
        $this->data .= $codex;

        return $this;
    }

    public function space($n, $char = ' ') {
        $clean_char = $char;
        if (strlen($clean_char) > 1) {
            $clean_char = substr($clean_char, 0, 1);
        }
        for ($i = 0; $i < $n; $i++) {
            $this->data .= $clean_char;
        }

        return $this;
    }

    public function text($text, $width = null, $align = 'L', $spacing = ' ') {
        if ($align == 'C') {
            return $this->textCenter($text, $width, $spacing);
        } elseif ($align == 'R') {
            return $this->textRight($text, $width, $spacing);
        } else {
            return $this->textLeft($text, $width, $spacing);
        }
    }

    public function textLeft($text, $width = null, $spacing = ' ') {
        if ($width == null) {
            $this->data .= $text;
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->data .= $newtext;
            } else {
                $this->data .= $text;
                $this->space($width - $len, $spacing);
            }
        }

        return $this;
    }

    public function textRight($text, $width = null, $spacing = ' ') {
        if ($width == null) {
            $this->data .= $text;
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->data .= $newtext;
            } else {
                $this->space($width - $len, $spacing);
                $this->data .= $text;
            }
        }

        return $this;
    }

    public function textCenter($text, $width = null, $spacing = ' ') {
        // $text = Normalizer::normalize($text);
        if ($width == null) {
            $this->data .= $text;
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->data .= $newtext;
            } else {
                $remain_space = $width - $len;
                $this->space(floor($remain_space / 2), $spacing);
                $this->data .= $text;
                $this->space(floor($remain_space / 2), $spacing);

                $this->space(($remain_space) % 2, $spacing);
            }
        }

        return $this;
    }

    public function render() {
        return $this->data;
    }

    public function setEmphasis(bool $on = true) {
        $this->addCode(CPrinter_EscPos::ESC . 'E' . ($on ? chr(1) : chr(0)));
    }

    public function setUnderline(int $underline = CPrinter_EscPos::UNDERLINE_SINGLE) {
        /* Set the underline */
        $this->addCode(CPrinter_EscPos::ESC . '-' . chr($underline));
    }

    /**
     * Print and feed line / Print and feed n lines.
     *
     * @param int $lines Number of lines to feed
     */
    public function feed(int $lines = 1) {
        if ($lines <= 1) {
            $this->addCode(CPrinter_EscPos::LF);
        } else {
            $this->addCode(CPrinter_EscPos::ESC . 'd' . chr($lines));
        }
    }

    /**
     * Some printers require a form feed to release the paper. On most printers, this
     * command is only useful in page mode, which is not implemented in this driver.
     */
    public function feedForm() {
        $this->addCode(CPrinter_EscPos::FF);
    }

    /**
     * Select print mode(s).
     *
     * Several MODE_* constants can be OR'd together passed to this function's `$mode` argument. The valid modes are:
     *  - Printer::MODE_FONT_A
     *  - Printer::MODE_FONT_B
     *  - Printer::MODE_EMPHASIZED
     *  - Printer::MODE_DOUBLE_HEIGHT
     *  - Printer::MODE_DOUBLE_WIDTH
     *  - Printer::MODE_UNDERLINE
     *
     * @param int $mode The mode to use. Default is Printer::MODE_FONT_A, with no special formatting. This has a similar effect to running initialize().
     */
    public function selectPrintMode(int $mode = CPrinter_EscPos::MODE_FONT_A) {
        $allModes = CPrinter_EscPos::MODE_FONT_B | CPrinter_EscPos::MODE_EMPHASIZED | CPrinter_EscPos::MODE_DOUBLE_HEIGHT | CPrinter_EscPos::MODE_DOUBLE_WIDTH | CPrinter_EscPos::MODE_UNDERLINE;
        if (!is_integer($mode) || $mode < 0 || ($mode & $allModes) != $mode) {
            throw new InvalidArgumentException('Invalid mode');
        }

        $this->addCode(CPrinter_EscPos::ESC . '!' . chr($mode));
    }

    /**
     * @return void
     */
    public function initialize() {
        $this->addCode(CPrinter_EscPos::ESC . '@');
    }
}
