<?php

	class CFormInputDropZone extends CFormInput {

		protected $multiple;
		protected $applyjs;
		protected $uniqid;
		protected $url;
		protected $autoProcessQueue;
		protected $removeLink;
		protected $parallelUpload;
		protected $maxFile;

		public function __construct($id) {
			parent::__construct($id);

			$this->multiple = 'true';
			$this->autoProcessQueue = 'false';
			$this->removeLink = 'true';
			$this->type = "dropzone";
			$this->applyjs = "dropzone";
	        $this->uniqid = uniqid();
		}

		public static function factory($id) {
			return new CFormInputDropZone($id);
		}

		public function set_multiple($bool) {
			$this->multiple = $bool;
			return $this;
		}

		public function set_applyjs($applyjs) {
			$this->applyjs = $applyjs;
			return $this;
		}

		public function set_url($url) {
			$this->url = $url;
			return $this;
		}

		public function set_autoProcess($bool) {
			$this->autoProcessQueue = $bool;
			return $this;
		}

		public function set_removeLink($bool) {
			$this->removeLink = $bool;
			return $this;
		}

		// public function set_parallelUpload($value) {
		// 	$this->parallelUpload = $value;
		// 	return $this;
		// }

		public function set_maxFile($value) {
			$this->maxFile = $value;
			$this->parallelUpload = $value;
			return $this;
		}

		public function processQueue() {

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
			$div_id = 'div_dropzone_' . $this->id;
			$html->appendln('
				<div id="'.$div_id.'" class="dropzone"></div>
			');
			return $html->text();
		}

		public function js($indent=0) {
			$div_id = 'div_dropzone_' . $this->id;
			$js = new CStringBuilder();
			$js->set_indent($indent);
			$url = (!empty($this->url)) ? $this->url : "unknown.php";
			$autoProcessQueue  = $this->autoProcessQueue;
			$uploadMultiple = $this->multiple;
			$addRemoveLinks = $this->removeLink;
			$parallelUploads = (!empty($this->parallelUpload)) ? $this->parallelUpload : 1;
			$maxFiles = (!empty($this->maxFile)) ? $this->maxFile : 1;
			if($this->applyjs == "dropzone") {
				$js->appendln('
					Dropzone.autoDiscover = false;
					var myDropzone = new Dropzone("#' . $div_id . '", {
					    url: "' . $url . '",
					    autoProcessQueue: ' . $autoProcessQueue . ',
					    uploadMultiple: ' . $uploadMultiple . ',
					    addRemoveLinks: ' . $addRemoveLinks . ',
					    parallelUploads: ' . $parallelUploads . ',
					    maxFiles: ' . $maxFiles . ',
					    sending: function(file, xhr, formData) {
					        var input = $("#' . $div_id . '").parents("form").find("input[type!=button]");
					        for (var i = 0; i < input.length; i++) { 
					            formData.append(input[i].name, input[i].value);
					        }
					    },
					})
					$("#' . $div_id . '").parents("form").find("input[type*=submit]").click(function(e) {
						e.preventDefault();
						myDropzone.processQueue();
					})
				');
			}
			return $js->text();
		}

	}
