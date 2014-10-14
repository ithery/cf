<?php

class CFormInputPassword extends CFormInput {

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "password";
    }

    public static function factory($id) {
        return new CFormInputPassword($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $html->appendln('<input type="password" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validation_class() . '" value="' . $this->value . '">')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        return "";
    }

}