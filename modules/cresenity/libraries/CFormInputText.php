<?php

class CFormInputText extends CFormInput {

    protected $vk;
    protected $vk_opt;
    protected $placeholder;
    protected $bootstrap;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = "text";
        $this->vk = false;
        $this->placeholder = "";
        $default_option = array(
            'layout' => 'qwerty',
            'restrictInput' => 'true',
            'preventPaste' => 'true',
            'autoAccept' => 'true',
        );
        $this->bootstrap = ccfg::get('bootstrap');
        if (strlen($this->bootstrap) == 0) {
            $this->bootstrap = '2';
        }

        $this->vk_opt = $default_option;
    }

    /**
     * 
     * @param type $id
     * @return \CFormInputText
     */
    public static function factory($id = '') {
        return new CFormInputText($id);
    }

    public function set_vk($bool) {
        $this->vk = $bool;
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function set_vk_opt($option) {
        $this->vk_opt = array_merge($this->vk_opt, $option);
        return $this;
    }

    public function set_name($name){
        $this->name = $name; return $this;
    }
    
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        
        if ($this->readonly)
            $disabled = ' readonly="readonly"';
        
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        
        if ($this->bootstrap == '3') {
            $classes = $classes ." form-control ";
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute.=" " . $k . '="' . $v . '"';
        }
        $html->appendln('<input type="text" placeholder="' . $this->placeholder . '" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $custom_css .$addition_attribute  .'>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js());
        if ($this->vk) {
            $js->append("$('#" . $this->id . "').keyboard(" . json_encode($this->vk_opt) . ");")->br();
        }


        return $js->text();
    }

}