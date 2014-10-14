<?php
class CFormInputFile extends CFormInput {
	protected $multiple;
	protected $applyjs;
	
	public function __construct($id) {
		parent::__construct($id);
		
		$this->multiple=false;
		$this->type="select";
		$this->applyjs="file-upload";
	}
	
	public static function factory($id) {
		return new CFormInputFile($id);
	}
	public function set_multiple($bool) {
		$this->multiple = true;
		return $this;
	}
	public function set_applyjs($applyjs) {
		$this->applyjs = $applyjs;
		return $this;
	}
	
	public function set_lookup($query) {
		
	}
	
	
	
	public function html($indent=0) {
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$disabled = "";
		if($this->disabled) $disabled = ' disabled="disabled"';
		$multiple = "";
		if($this->multiple) $multiple = ' multiple="multiple"';
		$name = $this->name;
		if($this->multiple) $name=$name."[]";
		$classes = $this->classes;
		$classes = implode(" ",$classes);
		if(strlen($classes)>0) $classes=" ".$classes;
		$custom_css = $this->custom_css;
		$custom_css = crenderer::render_style($custom_css);
		if(strlen($custom_css)>0) {
			$custom_css = ' style="'.$custom_css.'"';
		}
		
		$add_class="fileupload-new";
		if(strlen($this->value)>0) {
			$add_class="fileupload-exists";
		
		}
		if($this->applyjs=="file-upload") {
			$html->appendln('<div class="fileupload '.$add_class.'" data-provides="fileupload">');
			$html->appendln('	<div class="input-group">');
			$html->appendln('		<div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview">'.$this->value.'</span></div>');
			$html->appendln('		<span class="btn btn-file"><span class="fileupload-new">'.clang::__('Select file').'</span><span class="fileupload-exists">'.clang::__('Change').'</span>');
							
		} 
		$html->appendln('			<input type="file" name="'.$name.'" id="'.$this->id.'" class="file'.$classes.$this->validation->validation_class().'"'.$custom_css.$disabled.$multiple.' />')->inc_indent()->br();
		if($this->applyjs=="file-upload") {
			$html->appendln('		</span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">'.clang::__('Remove').'</a>');
			$html->appendln('	</div>');
			$html->appendln('</div>');
			
		}
		
		//$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
		return $html->text();	
	}
	public function js($indent=0) {
		
		$js = new CStringBuilder();
		$js->set_indent($indent);
		if($this->applyjs=="file-upload") {
			//$js->append("$('#".$this->id."').select2();")->br();
		}
		
		
		return $js->text();
		
	}
	
}