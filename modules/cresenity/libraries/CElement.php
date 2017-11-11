<?php

abstract class CElement extends CObservable {

    protected $classes;
    protected $tag;
    protected $body;
    protected $attr;
    protected $custom_css;
    protected $text;
    protected $checkbox;
    protected $radio;
    protected $bootstrap;
    protected $select2;
    protected $theme;
    protected $theme_style = array();
    protected $client_modules = array();
    protected $theme_data = array();
    protected $before;
    protected $after;
    protected $is_builded = false;
    protected $is_onetag = false;
    private $is_build = false;

    public static function valid_tag() {
        $available_tag = array('div', 'a', 'p', 'span');
    }

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id);

        $this->classes = array();
        $this->attr = array();
        $this->custom_css = array();
        $this->text = '';

        $this->tag = $tag;
        $this->theme = ctheme::get_current_theme();

        $this->before = null;
        $this->after = null;

        $this->is_builded = false;
        $this->is_onetag = false;



        $this->bootstrap = ccfg::get('bootstrap');
        if (strlen($this->bootstrap) == 0) {
            $this->bootstrap = '2';
        }

        $theme_data = CManager::instance()->get_theme_data();
        $this->theme_data = $theme_data;
        if (isset($theme_data)) {
            $this->select2 = carr::get($theme_data, 'select2');
            $this->bootstrap = carr::get($theme_data, 'bootstrap');
            $this->checkbox = carr::get($theme_data, 'checkbox', '0');
            $this->radio = carr::get($theme_data, 'radio', '0');
            $this->theme_style = carr::get($theme_data, 'theme_style');
            $this->client_modules = carr::get($theme_data, 'client_modules');
        }
        if (strlen($this->bootstrap) == 0) {
            $bootstrap = ccfg::get('bootstrap');
            $this->bootstrap = $bootstrap;
        }
    }

    public function set_radio($radio) {
        $this->radio = $radio;
        return $this;
    }

    public function set_text($text) {
        $this->text = $text;
    }

    public function custom_css($key, $val) {
        $this->custom_css[$key] = $val;
        return $this;
    }

    public function set_tag($tag) {
        $this->tag = $tag;
    }

    public function add_class($c) {
        if (is_array($c)) {
            $this->classes = array_merge($c, $this->classes);
        } else {
            if ($this->bootstrap == '3.3') {
                $c = str_replace('span', 'col-md-', $c);
                $c = str_replace('row-fluid', 'row', $c);
            }
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

    public function generate_class() {
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        return $classes;
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