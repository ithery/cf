<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated since 1.2
 */
class CUlElement extends CElement {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'ul';
    }

    public static function factory($id = '') {
        return new CUlElement($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = '';
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= ' ' . $k . '="' . $v . '"';
        }
        $html->appendln('<ul id="' . $this->id . '" class="' . $classes . '"' . $custom_css . $addition_attribute . '>');

        $html->appendln(parent::html($html->getIndent()))->br();
        $html->appendln('</ul>');

        return $html->text();
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }
}
