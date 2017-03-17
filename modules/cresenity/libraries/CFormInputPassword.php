<?php

class CFormInputPassword extends CFormInput {

    protected $autocomplete;
    protected $placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "password";
        $this->autocomplete = true;
        $this->placeholder = "";
    }

    public static function factory($id) {
        return new CFormInputPassword($id);
    }

    public function set_autocomplete($bool) {
        $this->autocomplete = $bool;
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        if ($this->bootstrap >= '3') {
            $classes = $classes . " form-control ";
        }
        $additional_attr = ' autocomplete="off"';
        if ($this->autocomplete) {
            $additional_attr = ' autocomplete="on"';
        }
        $html->appendln('<input type="password" placeholder="' . $this->placeholder . '" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '" ' . $additional_attr . '>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        return "";
    }

}
