<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 4:18:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element {

    public static function valid_tag() {
        return $this->validTag();
    }
    
    public function set_radio($radio) {
        return $this->setRadio($radio);
    }
    
    public function set_text($text) {
        return $this->setText($text);
    }
    
    public function custom_css($key, $val) {
        return $this->customCss($key, $val);
    }
    
    public function set_tag($tag) {
        return $this->setTag($tag);
    }
    
    public function add_class($c) {
        return $this->addClass($c);
    }
    
    public function delete_attr($k) {
        return $this->deleteAttr($k);
    }
    
    public function set_attr($k, $v) {
        return $this->setAttr($k, $v);
    }
    
    public function add_attr($k, $v) {
        return $this->addAttr($k, $v);
    }
    
    public function get_attr($k) {
        return $this->getAttr($k);
    }
    
    public function generate_class() {
        return $this->generateClass();
    }
    
    public static function is_instanceof($val) {
        return $this->isInstanceof($val);
    }
    
    public function toarray() {
        return $this->toArray();
    }
    
    protected function html_child($indent = 0) {
        return $this->htmlChild($indent = 0);
    }
    
    protected function js_child($indent = 0) {
        return $this->jsChild($indent = 0);
    }
}
