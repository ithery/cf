<?php

class CMobile_Element_Control_Textarea extends CMobile_Element_AbstractControl {

    protected $col;
    protected $row;
    protected $placeholder;
    protected $label_float;
    protected $prefix_icon;
    protected $length;

    public function __construct($id) {
        parent::__construct($id);

        $this->tag = "textarea";
        $this->placeholder = "";
        $this->prefix_icon = '';
        $this->label_float = true;
        $this->col = 60;
        $this->row = 10;
        $this->length = '';
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Textarea($id);
    }

    public function set_col($col) {
        $this->col = $col;
        return $this;
    }

    public function set_row($row) {
        $this->row = $row;
        return $this;
    }

    public function set_length($length) {
        $this->length = $length;
        return $this;
    }

    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        $placeholder = "";
        $cols = "";
        $rows = "";
        $length = "";
        
        if ($this->placeholder)
            $placeholder = ' placeholder="'.$this->placeholder.'"';
        if ($this->col)
            $cols = ' cols="'.$this->col.'"';
        if ($this->row)
            $rows = ' cols="'.$this->row.'"';            
        if (strlen($this->length) > 0)
            $length = ' length="'.$this->length.'"';
                    
        $html_attr .= $length;
        $html_attr .= $cols;
        $html_attr .= $rows;
        return $html_attr;
    }
    public function build($indent = 0) {
        $this->add_class('input-field');
        $this->add_class('materialize-textarea');
        $this->add_class( $this->validation->validation_class());
        $html_attr = $this->html_attr();
        if (strlen($this->prefix_icon) > 0) {
            $this->before()->add_icon()->set_icon($this->prefix_icon)->set_type('prefix'); 
        }
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