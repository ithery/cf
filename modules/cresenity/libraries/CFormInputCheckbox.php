<?php

class CFormInputCheckbox extends CFormInput {

    protected $checked = "";
    protected $label = "";
    protected $applyjs = "";

    public function __construct($id) {
        parent::__construct($id);


        $this->type = "checkbox";
        $this->label = "";
        $this->applyjs = "uniform";
        $this->checked = false;
    }

    public static function factory($id) {
        return new CFormInputCheckbox($id);
    }

    public function set_applyjs($applyjs) {
        $this->applyjs = $applyjs;
        return $this;
    }

    public function set_checked($bool) {
        $this->checked = $bool;
        return $this;
    }

    public function set_label($label,$lang=true) {
		if($lang==true) {
			$label = clang::__($label);
		}
        $this->label = $label;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        $checked = "";
        if ($this->checked)
            $checked = ' checked="checked"';
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $html->append('<label class="checkbox">');
        if ($this->applyjs == "switch") {
            $html->append('<div class="switch">');
        }

        $html->append('<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $checked . '>');
        if (strlen($this->label) > 0) {
            $html->appendln('&nbsp;' . $this->label);
        }
        if ($this->applyjs == "switch") {
            $html->append('</div>');
        }
        $html->append('</label>');
        $html->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
		$js->append(parent::js($indent))->br();
        if ($this->applyjs == "uniform") {
            //$js->append("$('#".$this->id."').uniform();")->br();
        }
        if ($this->applyjs == "switch") {
            //$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
        }


        return $js->text();
    }

}