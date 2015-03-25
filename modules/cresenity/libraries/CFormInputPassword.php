<?php

class CFormInputPassword extends CFormInput {
	
	protected $autocomplete;
	
    public function __construct($id) {
        parent::__construct($id);
        $this->type = "password";
		$this->autocomplete = true;
    }

    public static function factory($id) {
        return new CFormInputPassword($id);
    }
	
	public function set_autocomplete($bool) {
        $this->autocomplete = $bool;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
		$additional_attr = ' autocomplete="off"';
		if($this->autocomplete) {
			$additional_attr=' autocomplete="on"';
		}
        $html->appendln('<input type="password" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validation_class() . '" value="' . $this->value . '" '.$additional_attr.'>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        return "";
    }

}