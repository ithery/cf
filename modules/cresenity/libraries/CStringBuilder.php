<?php

class CStringBuilder {

    private $text = "";
    private $indent = 0;

    public function __construct($str = "") {
        $this->text = $str;
    }

    public static function factory() {
        return new CStringBuilder();
    }

    public function set_indent($ind) {
        $this->indent = $ind;
        return $this;
    }

    public function get_indent() {
        return $this->indent;
    }

    public function inc_indent($n = 1) {
        $this->indent+=$n;
        return $this;
    }

    public function dec_indent($n = 1) {
        $this->indent-=$n;
        return $this;
    }

    public function append($str) {
        $this->text.=$str;
        return $this;
    }

    public function appendln($str) {
        $this->text.= cutils::indent($this->indent);
        return $this->append($str);
    }

    public function br() {
        $this->text.= "\r\n";
        return $this;
    }

    public function text() {
        return $this->text;
    }

}

?>