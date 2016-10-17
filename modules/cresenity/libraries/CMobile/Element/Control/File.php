<?php

class CMobile_Element_Control_File extends CMobile_Element_AbstractControl {

    protected $placeholder;
    protected $label_float;
    protected $prefix_icon;

    public function __construct($id) {
        parent::__construct($id);

        $this->tag = "div";
        $this->placeholder = "";
        $this->prefix_icon = '';
        $this->label_float = true;
    }

    public static function factory($id) {
        return new CMobile_Element_Control_File($id);
    }

    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();

        return $html_attr;
    }
    public function build($indent = 0) {
        $div_btn = $this->add_div()->add_class('btn');
        $div_btn->add_span()->add(clang::__('FILE'));
        $div_btn->add_control('file', 'file');
        $div_file_path_wrapper = $this->add_div()->add_class("file-path-wrapper");
        $div_file_path_wrapper->add_control('file_text', 'text')->add_class('file-path validate');
    }

    public function js($indent = 0) {
        
        $js =  "
          $('#" . $this->id . "').val('" . $this->value . "');
          $('#" . $this->id . "').trigger('autoresize');
        ";
        $js.=parent::js();
        return $js;
    }

}