<?php
class CFormInputLabel extends CFormInput {
	
	public function __construct($id) {
		parent::__construct($id);
		
		$this->type="label";
		
	}
	public static function factory($id) {
		return new CFormInputLabel($id);
	}
	public function html($indent=0) {
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$disabled = "";
		
		if($this->disabled) $disabled = ' disabled="disabled"';
		
		$classes = $this->classes;
		$classes = implode(" ",$classes);
		if(strlen($classes)>0) $classes=" ".$classes;
		$custom_css = $this->custom_css;
		$custom_css = crenderer::render_style($custom_css);
		if(strlen($custom_css)>0) {
			$custom_css = ' style="'.$custom_css.'"';
		}
		if(is_array($this->value)) {
			$i=0;
			foreach($this->value as $val) {
				$new_val = $val;
				foreach($this->transforms as $trans) {
					$new_val = $trans->execute($new_val);
				}
				$html->appendln('<span class="label" id="'.$this->id.'_'.$i.'">'.$new_val.'</span>')->br();
				$i++;
			}
		} else {
			$new_val = $this->value;
			foreach($this->transforms as $trans) {
				$new_val = $trans->execute($new_val);
			}
			$html->appendln('<span class="label'.$classes.'" name="'.$this->name.'" id="'.$this->id.'" '.$custom_css.'>'.$new_val.'</span>')->br();
		}
		return $html->text();	
	}
	public function js($indent=0) {
		return "";
	}
}