<?php

abstract class CMobile_Element extends CMobile_Observable {

    protected $classes;
    protected $tag;
    protected $body;
    protected $attr;
    protected $custom_css;
    protected $bootstrap;
    protected $theme;
    protected $before;
    protected $after;
    protected $is_builded = false;
    protected $is_empty = false;

    public static function valid_tag() {
        $available_tag = array('div', 'a', 'p', 'span');
    }

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id);

        $this->classes = array();
        $this->attr = array();
        $this->custom_css = array();

        $this->tag = $tag;
        $this->theme = ccfg::get('theme');

        $this->before = null;
        $this->after = null;

        $this->is_builded = false;
        $this->is_empty = false;
    }

    public function custom_css($key, $val) {
        $this->custom_css[$key] = $val;
        return $this;
    }

    protected function set_tag($tag) {
        $this->tag = $tag;
    }

    public function add_class($c) {
        if ($this->bootstrap == '3') {
            if ($this->theme == 'ittron-app') {
                $c = str_replace('span', 'col-md-', $c);
            }
        }

        if (is_array($c)) {
            $this->classes = array_merge($c, $this->classes);
        } else {
            $this->classes[] = $c;
        }
        return $this;
    }

    public function delete_attr($k) {
        if (isset($this->attr[$k])) {
            unset($this->attr[$k]);
        }
        return $this;
    }

    public function set_attr($k, $v) {
        $this->attr[$k] = $v;
        return $this;
    }

    public function add_attr($k, $v) {
        return $this->set_attr($k, $v);
    }

    public function get_attr($k) {
        if (isset($this->attr[$k])) {
            return $this->attr[$k];
        }
        return null;
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

    public static function is_instanceof($val) {
        if (is_object($val)) {
            return ($val instanceof CElement);
        }
        return false;
    }

    public function toarray() {
        if (!empty($this->classes)) {
            $data['attr']['class'] = implode(" ", $this->classes);
        }
        $data['attr']['id'] = $this->id;

        $data['tag'] = $this->tag;
        if (strlen($this->text) > 0) {
            $data['text'] = $this->text;
        }
        $data = array_merge_recursive($data, parent::toarray());
        return $data;
    }

    protected function html_child($indent = 0) {
        return parent::html($indent);
    }

    protected function js_child($indent = 0) {
        return parent::js($indent);
    }

    protected function html_attr() {
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
        $class_attr = ' class="' . $classes . '"';
        $html_attr = 'id="' . $this->id . '" ' . $class_attr . $custom_css . $addition_attribute;
        return $html_attr;
    }

    public function before_html($indent = 0) {
        return $this->before()->html($indent);
    }

    public function after_html($indent = 0) {
        return $this->after()->html($indent);
    }

    public function before() {
        if ($this->before == null) {
            $this->before = CMobile_PseudoElement::factory();
        }
        return $this->before;
    }

    public function after() {
        if ($this->after == null) {
            $this->after = CMobile_PseudoElement::factory();
        }
        return $this->after;
    }

    protected function build_once() {
        //just build once
        if (!$this->is_builded) {
            $this->build();
            $this->is_builded = true;
        }
    }

    protected function build() {
        
    }

    private $is_build = false;
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        if (!$this->is_build) {
            $this->build();
            $this->is_build = true;
        }
        $html->appendln($this->before_html());
        if ($this->is_empty) {
            $html->appendln($this->onetag());
        } else {
            $html->appendln($this->pretag());
            $html->appendln(parent::html($html->get_indent()))->br();
            $html->appendln($this->posttag());
        }
        $html->appendln($this->after_html());

        return $html->text();
    }

    public function js($indent = 0) {
        if (!$this->is_build) {
            $this->build();
            $this->is_build = true;
        }
        $js = '';
        $js .= $this->before()->js($indent);
        $js .= parent::js($indent);
        $js .= $this->after()->js($indent);
        return $js;
    }

    public function __to_string() {
        $return = "<h3> HTML </h3>"
                . "<pre>"
                . "<code>"
                . htmlspecialchars($this->html())
                . "</code>"
                . "</pre>";
        $return .= "<h3> JS </h3>"
                . "<pre>"
                . "<code>"
                . htmlspecialchars($this->js())
                . "</code>"
                . "</pre>";
        return $return;
    }

}

?>