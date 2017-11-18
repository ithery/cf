<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CLiElement extends CElement {

    public function __construct($id = "") {
        cdbg::deprecated('CLiElement is deprecated');
        parent::__construct($id);
        $this->tag = "li";
    }

    public static function factory($id = "") {
        return new CLiElement($id);
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
        $html->appendln('<li id="' . $this->id . '" class="' . $classes . '"' . $custom_css . $addition_attribute . '>');


        $html->appendln(parent::html($html->get_indent()))->br();
        $html->appendln('</li>');

        return $html->text();
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }

}
