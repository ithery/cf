<?php

class CString_PatternBuilder implements Stringable {
    protected $fragments = [];

    public function construct() {
        $this->fragments = [];
    }

    public function any() {
        $this->fragments[] = '.*';

        return $this;
    }

    public function or() {
        $this->fragments[] = '|';

        return $this;
    }

    public function __toString() {
        return implode('', $this->fragments);
    }

    public function text($s) {
        $s = preg_replace('#([\\\\.\\[{()*+?^$|])#', '\\\\$1', $s);
        $this->fragments[] = $s;

        return $this;
    }

    public function number($s) {
        $s = str_replace(['dddd', 'ddd', 'dd'], ['d{4}', 'd{3}', 'd{2}'], $s);
        $s = str_replace(['xxxx', 'xxx', 'xx'], ['x{4}', 'x{3}', 'x{2}'], $s);
        $s = str_replace('d', '\\d', $s);
        $s = str_replace('x', '[0-9a-fA-F]', $s);
        $s = preg_replace('#([.])#', '\\\\$1', $s);
        $s = preg_replace('#\\|$#', '\\\\|', $s);
        $s = preg_replace('#^\\|#', '\\\\|', $s);// special case for delimiter
        // special case for delimiter

        $this->fragments[] = $s;

        return $this;
    }

    public function optional($count = 1) {
        array_splice($this->fragments, count($this->fragments) - $count, 0, ['(?:']);

        $this->fragments[] = ')?';

        return $this;
    }

    public function expression($s) {
        $s = preg_replace('#\\|$#', '\\\\|', $s); // special case for delimiter

        $this->fragments[] = $s;

        return $this;
    }

    public function groupBegin() {
        return $this->expression('(?:');
    }

    public function groupEnd($s = '') {
        return $this->expression(')' . $s);
    }
}
