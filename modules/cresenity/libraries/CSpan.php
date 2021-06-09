<?php

defined('SYSPATH') or die('No direct access allowed.');

class CSpan extends CElement_Element {
    protected $col;
    protected $size;

    public function __construct($id = '') {
        parent::__construct($id);

        $this->size = 'md';
        $this->col = 12;
    }

    public static function factory($id = '') {
        return new CSpan($id);
    }

    public function set_col($col) {
        $this->col = $col;
        return $this;
    }

    public function set_size($size) {
        $this->size = $size;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = '';
        if ($this->bootstrap == '3.3') {
            $html->appendln('<div class="col-' . $this->size . '-' . $this->col . '">');
        } else {
            $html->appendln('<div class="span' . $this->col . '">');
        }
        $html->appendln($this->html_child($html->get_indent()))->br();
        $html->appendln('</div>');

        return $html->text();
    }
}
