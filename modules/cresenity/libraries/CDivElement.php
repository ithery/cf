<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CDivElement extends CElement {

    public function __construct($id = "", $tag = "div") {

        parent::__construct($id,$tag);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CDivElement($id);
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
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute.=" " . $k . '="' . $v . '"';
        }
        $html->appendln('<div id="' . $this->id . '" class="' . $classes . '"' . $custom_css . $addition_attribute . '>');


        $html->appendln(parent::html($html->get_indent()))->br();
        $html->appendln('</div>');

        return $html->text();
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }

}
