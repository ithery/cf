<?php
class CMobile_Element_Control_Select extends CMobile_Element_AbstractControl {

    protected $group_list = array();
    protected $multiple;
    protected $applyjs;
    protected $prefix_icon;
    protected $list;
    protected $placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = "select";
        $this->multiple = false;
        $this->applyjs = "false";
        $this->prefix_icon = '';
        $this->list = array();
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Select($id);
    }

    public function set_multiple($bool) {
        $this->multiple = $bool;
        return $this;
    }

    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }

    public function set_list($list) {
        $this->list = $list;
        return $this;
    }

    public function set_group_list($group_list) {
        $this->group_list = $group_list;
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        $multiple = "";
        
        if ($this->multiple)
            $multiple = 'multiple';
                
        $html_attr .= $multiple;
        return $html_attr;
    }

    public function build($indent = 0) {
        $this->add_class( $this->validation->validation_class());
        $html_attr = $this->html_attr();
        if (strlen($this->placeholder)>0) {
            $this->set_attr('placeholder',$this->placeholder);
        }
        if (strlen($this->prefix_icon) > 0) {
            $this->before()->add_icon()->set_icon($this->prefix_icon)->set_type('prefix'); 
        }
        if ($this->list != null) {
            foreach ($this->list as $k => $v) {
                $data_icon = '';
                $class_option = '';
                if(isset($v['icon_position'])) {
                    if($v['icon_position'] == 'left') {
                        $class_option .= ' left';
                    }
                }
                if(isset($v['icon'])) {
                    $data_icon = $v['icon'];
                    $class_option .= ' circle';
                }
                $selected = "";
                if (strlen($this->value) > 0) {
                    if ($this->value == $k) {
                        $selected = "selected";
                    }
                }
                $this->add('<option value="' . $k . '" data-icon="' . $data_icon . '" class="' . $class_option . '" '.$selected.'>' . $v['text'] . '</option>');
            }
        }
        if ($this->group_list != null) {
            foreach ($this->group_list as $k_group => $list) {
                $this->add('<optgroup label="' . $k_group . '">');
                foreach ($list as $k_list => $v) {
                    $data_icon = '';
                    $class_option = '';
                    if(isset($v['icon_position'])) {
                        if($v['icon_position'] == 'left') {
                            $class_option .= ' left';
                        }
                    }
                    if(isset($v['icon'])) {
                        $data_icon = $v['icon'];
                        $class_option .= ' circle';
                    }
                    $this->add('<option value="' . $k_list . '" data-icon="' . $data_icon . '" class="' . $class_option . '">' . $v['text'] . '</option>');
                }
                $this->add('</optgroup>');
            }
        }
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js($indent))->br();
        $js->append("$('#" . $this->id . "').material_select();");
        return $js->text();
    }

}
