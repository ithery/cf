<?php

abstract class CElement extends CObservable {

    use CTrait_Compat_Element;

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
    protected $theme_data = array();
    protected $before;
    protected $after;
    protected $is_empty = false;

    public static function validTag() {
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
        $theme_data = CManager::instance()->getThemeData();
        $this->theme_data = $theme_data;

        if (isset($theme_data)) {
            $this->select2 = carr::get($theme_data, 'select2');
            $this->bootstrap = carr::get($theme_data, 'bootstrap');
            $this->checkbox = carr::get($theme_data, 'checkbox', '0');
            $this->radio = carr::get($theme_data, 'radio', '0');
            $this->theme_style = carr::get($theme_data, 'theme_style');
        }
        if (strlen($this->bootstrap) == 0) {
            $bootstrap = ccfg::get('bootstrap');
            $this->bootstrap = $bootstrap;
        }
    }

    public function setRadio($radio) {
        $this->radio = $radio;
        return $this;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function customCss($key, $val) {
        $this->custom_css[$key] = $val;
        return $this;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    /**
     * Add class attribute for the element
     * 
     * @param string $c
     * @return $this
     */
    public function addClass($c) {
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

    public function removeClass($class) {
        if (!is_array($class)) {
            $class = array($class);
        }

        foreach ($class as $c) {
            foreach ($this->classes as $key => $value) {
                if ($c == $value) {
                    unset($this->classes[$key]);
                }
            }
        }

        return $this;
    }

    public function deleteAttr($k) {
        if (isset($this->attr[$k])) {
            unset($this->attr[$k]);
        }
        return $this;
    }

    public function setAttr($k, $v) {
        $this->attr[$k] = $v;
        return $this;
    }

    public function setAttrFromArray($arr) {
        foreach ($arr as $k => $v) {
            $this->attr[$k] = $v;
        }

        return $this;
    }

    public function addAttr($k, $v) {
        return $this->setAttr($k, $v);
    }

    public function getAttr($k) {
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

    public function generateClass() {
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        return $classes;
    }

    public function toArray() {
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

    protected function htmlChild($indent = 0) {
        return parent::html($indent);
    }

    protected function jsChild($indent = 0) {
        return parent::js($indent);
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
