<?php

class CElement_FormInput_Text extends CElement_FormInput {

    protected $vk;
    protected $vk_opt;
    protected $placeholder;
    protected $bootstrap;
    protected $input_style;
    protected $button_position;
    protected $action;

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
        $this->vk_opt = $default_option;
        $this->input_style = 'default';
        $this->button_position = null;
        $this->action = null;
    }

    /**
     * 
     * @param type $id
     * @return \CFormInputText
     */
    public static function factory($id = '') {
        return new CElement_FormInput_Text($id);
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

    public function set_name($name) {
        $this->name = $name;
        return $this;
    }

    function get_input_style() {
        return $this->input_style;
    }

    function get_button_position() {
        return $this->button_position;
    }

    function get_action() {
        return $this->action;
    }

    function set_input_style($input_style) {
        $this->input_style = $input_style;
        return $this;
    }

    function set_button_position($button_position) {
        $this->button_position = $button_position;
        return $this;
    }

    function add_action($id = '') {
        $this->action = CAction::factory($id);
        return $this->action;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }

        if ($this->readonly) {
            $disabled = ' readonly="readonly"';
        }

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;

        if ($this->bootstrap >= '3') {
            $classes = $classes . " form-control ";
            if ($this->input_style == 'input-group') {
                $html->appendln('<div class="input-group">');
                if ($this->button_position == 'left') {
                    $html->appendln('<span class="input-group-btn">');
                    if ($this->action !== null) {
                        $html->appendln($this->action->html());
                    }
                    $html->appendln('</span>');
                }
            }
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
        }
        $html->appendln('<input type="text" placeholder="' . $this->placeholder . '" name="' . $this->name . '" id="' . $this->id . '" class="form-control input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $custom_css . $addition_attribute . '/>')->br();

        if ($this->bootstrap >= '3') {
            if ($this->button_position == 'right') {
                $html->appendln('<span class="input-group-btn">');
                if ($this->action !== null) {
                    $html->appendln($this->action->html());
                }
                $html->appendln('</span>');
            }
            if ($this->input_style == 'input-group') {
                $html->appendln('</div>');
            }
        }
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);

        if ($this->action != null) {
            $js->appendln($this->action->js());
        }

        $js->append(parent::js());
        if ($this->vk) {
            $js->append("$('#" . $this->id . "').keyboard(" . json_encode($this->vk_opt) . ");")->br();
        }


        return $js->text();
    }

}
