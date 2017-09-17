<?php

class CElement extends CObservable {

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
    protected $is_empty = false;
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
        $this->bootstrap = ccfg::get('bootstrap');
        if (strlen($this->bootstrap) == 0) {
            $this->bootstrap = '2';
        }
        $this->theme = ccfg::get('theme');
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

    public static function factory($id = "", $tag = "div") {
        return new CElement($id, $tag);
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

    public function pretag() {

        return '<' . $this->tag . '>';
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