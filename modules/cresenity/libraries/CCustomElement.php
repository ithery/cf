<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CCustomElement extends CElement {

    protected $tag;

    public function __construct($tag, $id = "") {

        parent::__construct($id);


        $this->tag = $tag;
    }

    public static function factory($tag, $id = "") {
        return new CCustomElement($tag, $id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        
        $attr = '';
        foreach ($this->attr as $k => $v) {
            $attr.=$k . '="' . $v . '" ';
        }
        
        $html->appendln('<' . $this->tag . ' id="' . $this->id . '" class="' . $classes . '"' . $custom_css . ' '.$attr.' >');


        $html->appendln(parent::html($html->get_indent()))->br();
        $html->appendln('</' . $this->tag . '>');

        return $html->text();
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }

}
