<?php

class CPrinter_EscPos_Builder extends CPrinter_EscPos_Printer {
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

    public function __construct() {
        $connector = new CPrinter_EscPos_PrintConnector_DummyPrintConnector();
        parent::__construct($connector);
    }

    public static function factory() {
        return new CPrinter_EscPos_Builder();
    }

    public function br() {
        return $this->feed(1);
    }

    public function space($n, $char = ' ') {
        $clean_char = $char;
        $data = '';
        if (strlen($clean_char) > 1) {
            $clean_char = substr($clean_char, 0, 1);
        }
        for ($i = 0; $i < $n; $i++) {
            $data .= $clean_char;
        }

        $this->connector->write($data);

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
            $this->connector->write($text);
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->connector->write($newtext);
            } else {
                $this->connector->write($text);
                $this->space($width - $len, $spacing);
            }
        }

        return $this;
    }

    public function textRight($text, $width = null, $spacing = ' ') {
        if ($width == null) {
            $this->connector->write($text);
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->connector->write($newtext);
            } else {
                $this->space($width - $len, $spacing);
                $this->connector->write($text);
            }
        }

        return $this;
    }

    public function textCenter($text, $width = null, $spacing = ' ') {
        // $text = Normalizer::normalize($text);
        if ($width == null) {
            $this->connector->write($text);
        } else {
            $len = strlen($text);
            if ($len > $width) {
                $newtext = substr($text, 0, $width);
                $this->connector->write($newtext);
            } else {
                $remain_space = $width - $len;
                $this->space(floor($remain_space / 2), $spacing);
                $this->connector->write($text);
                $this->space(floor($remain_space / 2), $spacing);

                $this->space(($remain_space) % 2, $spacing);
            }
        }

        return $this;
    }

    public function render() {
        $connector = $this->connector;
        /** @var CPrinter_EscPos_PrintConnector_DummyPrintConnector $connector */
        $data = $connector->getData();
        $connector->finalize();

        return $data;
    }
}
