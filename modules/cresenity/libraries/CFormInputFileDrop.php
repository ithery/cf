<?php
class CFormInputFileDrop extends CFormInput {
	protected $multiple;
	protected $applyjs;
	protected $uniqid;

	public function __construct($id) {
		parent::__construct($id);

		$this->multiple=false;
		$this->type="filedrop";
		$this->applyjs="dropzone";
        $this->uniqid = uniqid();
		CClientModules::instance()->register_module('dropzone');
	}

	public static function factory($id) {
		return new CFormInputFileDrop($id);
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
		$div_id = 'div_filedrop_'.$this->id;
		$html->appendln('
			<div id="'.$div_id.'" class="dropzone">
				<div class="dz-message">
					Drop files here or click to upload.<br />
					<input type="hidden" id="'.$this->id.'" name="'.$this->id.'" value="'.$this->id."_".$this->uniqid.'" />
				</div>
			</div>
		');
		//$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
		return $html->text();
	}
	public static function handle_file_drop($data) {
		$session = Session::instance();
		$id = $data->element_id;
		$uniqid = $data->uniqid;
		$files = array();
		if($session->get($id."_".$uniqid.'_filedrop')) {
			$files = $session->get($id."_".$uniqid.'_filedrop');
		}

        $temp_path = 'temp/document/';
        foreach($_FILES as $f) {
            move_uploaded_file($f['tmp_name'], $temp_path.DIRECTORY_SEPARATOR.str_replace(' ','_',$f['name']));
			$f['tmp_name'] 	= $temp_path;
            $files[] = $f;
		}
		$session->set($id."_".$uniqid.'_filedrop',$files);
	}



	public function create_ajax_url() {
		return CAjaxMethod::factory()
			->set_type('callback')
			->set_data('callable',array('CFormInputFileDrop','handle_file_drop'))
			->set_data('element_id',$this->id)
			->set_data('uniqid',$this->uniqid)
			->makeurl();
	}

	public static function get_files($id) {
		$session = Session::instance();
		$files = array();
		if($session->get($id.'_filedrop')) {
			$files = $session->get($id.'_filedrop');
		}
		return $files;
	}

	public function js($indent=0) {
		$div_id = 'div_filedrop_'.$this->id;
		$ajax_url = $this->create_ajax_url();
		$js = new CStringBuilder();
		$js->set_indent($indent);
		if($this->applyjs=="dropzone") {
			$js->appendln("
				$('#".$div_id."').dropzone({url:'".$ajax_url."'});

			");
		}


		return $js->text();

	}

}
