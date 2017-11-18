<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 12, 2017, 3:34:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CElement_Element extends CElement {

    protected $before;
    protected $after;
    protected $is_builded = false;
    protected $is_onetag = false;
    private $is_build = false;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id);

        $this->theme = ctheme::get_current_theme();

        $this->before = null;
        $this->after = null;

        $this->is_builded = false;
        $this->is_onetag = false;



        $this->bootstrap = ccfg::get('bootstrap');
        if (strlen($this->bootstrap) == 0) {
            $this->bootstrap = '2';
        }
    }

    public function onetag() {
        return '<' . $this->tag . ' ' . $this->html_attr() . ' />';
    }

    public function pretag() {
        return '<' . $this->tag . ' ' . $this->html_attr() . ' >';
    }

    public function posttag() {
        return '</' . $this->tag . '>';
    }

    protected function html_attr() {
        $classes = $this->classes;
        $classes = implode(" ", $classes);


        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
        }
        $class_attr = ' class="' . $classes . '"';
        $html_attr = 'id="' . $this->id . '" ' . $class_attr . $custom_css . $addition_attribute;
        return $html_attr;
    }

    public static function is_instanceof($val) {
        if (is_object($val)) {
            return ($val instanceof CElement);
        }
        return false;
    }

    protected function build_once() {
        //just build once
        if (!$this->is_builded) {
            $this->build();
            $this->is_builded = true;
        }
    }

    protected function html_child($indent = 0) {
        return parent::html($indent);
    }

    protected function js_child($indent = 0) {
        return parent::js($indent);
    }

    public function before_html($indent = 0) {
        return $this->before()->html($indent);
    }

    public function after_html($indent = 0) {
        return $this->after()->html($indent);
    }

    public function before_js($indent = 0) {
        return $this->before()->js($indent);
    }

    public function after_js($indent = 0) {
        return $this->after()->js($indent);
    }

    public function before() {
        if ($this->before == null) {
            $this->before = CElement_PseudoElement::factory();
        }
        return $this->before;
    }

    public function after() {
        if ($this->after == null) {
            $this->after = CElement_PseudoElement::factory();
        }
        return $this->after;
    }

    protected function build() {
        
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
 
        $html->set_indent($indent);
        $this->build_once();
        $html->appendln($this->before_html($indent));
        if ($this->is_onetag) {
            $html->appendln($this->onetag());
        } else {
            
            $html->appendln($this->pretag())->br();
            $html->inc_indent();
            $html->appendln($this->html_child($html->get_indent()))->br();
            $html->dec_indent();
            $html->appendln($this->posttag())->br();
        }
        $html->appendln($this->after_html($indent));

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $this->build_once();

        $js->appendln($this->before_js($js->get_indent()));
        $js->appendln($this->js_child($js->get_indent()))->br();
        $js->appendln($this->after_js($js->get_indent()));

        return $js->text();
    }

}
