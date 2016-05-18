<?php
class CMobile_Element_Control_Input_Checkbox extends CMobile_Element_Control_AbstractInput {
	protected $checked;
	protected $label;
	protected $applyjs;
	
	public function __construct($id) {
		parent::__construct($id);
		
		$this->type="checkbox";
		$this->applyjs="uniform";
		$this->label="";
		$this->checked=false;
	}
	public static function factory($id) {
		return new CMobile_Element_Control_Input_Checkbox($id);
	}

	public function set_label($text, $lang = true) {
        if ($lang) $text = clang::__($text);
        $this->label = $text;
        return $this;
    }

	public function set_applyjs($applyjs) {
		$this->applyjs = $applyjs;
		return $this;
	}

	public function set_checked($bool) {
		$this->checked = $bool;
		return $this;
	}

	protected function build() {
		$this->set_attr('type',$this->type);
		
		$this->add_class('validate');
                if($this->checked) {
			$this->add_attr('checked', '');
		}

		// if ($this->show_label) {
            $this->add_label()->set_label($this->label)->set_for_id($this->id);
            //$html->appendln('<label id="' . $this->id . '" class="' . $label_class . '">' . $this->label . '</label>')->br();
        // }
		
	}



	// public function html($indent=0) {
	// 	$html = new CStringBuilder();
	// 	$html->set_indent($indent);
	// 	$disabled = "";
	// 	$checked = "";
	// 	if($this->checked) $checked =  ' checked="checked"';
	// 	if($this->disabled) $disabled = ' disabled="disabled"';
		
	// 	$classes = $this->classes;
	// 	$classes = implode(" ",$classes);
	// 	if(strlen($classes)>0) $classes=" ".$classes;
	// 	$custom_css = $this->custom_css;
	// 	$custom_css = crenderer::render_style($custom_css);
	// 	if(strlen($custom_css)>0) {
	// 		$custom_css = ' style="'.$custom_css.'"';
	// 	}
		
	// 	$html->append('<label class="checkbox'.$classes.'" >');
	// 	if($this->applyjs=="switch") {
	// 		$html->append('<div class="switch">');
	// 	}
		
	// 	$html->append('<input type="radio" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.$checked.'>');
	// 	if(strlen($this->label)>0) {
	// 		$html->appendln('&nbsp;'.$this->label);
	// 	}
	// 	if($this->applyjs=="switch") {
	// 		$html->append('</div>');
	// 	}
	// 	$html->append('</label>');
	// 	$html->br();
	// 	return $html->text();	
	// }
	// public function js($indent=0) {
	// 	$js = new CStringBuilder();
	// 	$js->set_indent($indent);
	// 	$js->append(parent::js($indent))->br();
	// 	if($this->applyjs=="uniform") {
	// 		//$js->append("$('#".$this->id."').uniform();")->br();
	// 	}
	// 	if($this->applyjs=="switch") {
	// 		//$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
	// 	}
		
		
	// 	return $js->text();
	// }
	
	
}