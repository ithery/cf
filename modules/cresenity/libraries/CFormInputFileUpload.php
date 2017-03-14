<?php

	class CFormInputFileUpload extends CFormInput {

		protected $applyjs;
		protected $uniqid;
		protected $removeLink;
		protected $disabled;
		protected $files;

		public function __construct($id) {
			parent::__construct($id);

			$this->removeLink = 'true';
			$this->type = "fileupload";
			$this->applyjs = "fileupload";
	        $this->uniqid = uniqid();
		}

		public static function factory($id) {
			return new CFormInputFileUpload($id);
		}

		public function set_applyjs($applyjs) {
			$this->applyjs = $applyjs;
			return $this;
		}

		public function set_removeLink($bool) {
			$this->removeLink = $bool;
			return $this;
		}

		public function set_files($files) {
			$this->files = $files;
			return $this;
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
			$div_id = 'div_fileupload_' . $this->id;
			$html->appendln('
				<style>
					#' . $div_id . ' {
						margin-left: 0px;
						padding: 10px 10px;
						border: 2px solid black;
					}
					#' . $div_id . ' div {
						border: 2px solid black;
						margin: 10px 10px;
						width: 100px;
						height: 100px;
						float: left;
						text-align: center;
						position: relative;
					}
					#' . $div_id . ' div img {
						width: 100%;
						height: 100%;
					}
					#' . $div_id . ' div span {
						position: absolute;
						left: 0;
						right: 0;
						top: 50%;
						color: white;
						font-size: 100%;
						background-color: rgba(0, 0, 0, 0.5);
					}
					#' . $div_id . '_description {
						text-align: center;
						display: block;
						font-size: 20px;
					}
					#' . $div_id . '_message {
						margin-left: 0px;
						display: none;
						font-size: 20px;
					}
					.' . $div_id . '_remove {
						cursor: pointer;
					}
				</style>
				<input id="' . $div_id . '_input" type="file" name="files[]" multiple style="display:none;">
				<div id="' . $div_id . '_message" class="row alert alert-danger fade in">

				</div>
				<div id="' . $div_id . '" class="row">
					<span id="' . $div_id . '_description">Click Here or Drop Files Here</span>
				</div>
			');
			return $html->text();
		}

		public function js($indent=0) {
			$div_id = 'div_fileupload_' . $this->id;
			$js = new CStringBuilder();
			$js->set_indent($indent);
			if($this->applyjs == "fileupload") {
				$js->appendln('
					var files = [];
					// var url = "http://ittronads.local/assets/image/NjI2MlJXUFNDXkJtX19XVVNtX0ZCQFlcV1ZFbQQCBwUGAAQDBwYDBwILaVtbVURXRRxcQlE=";
					// var test = new File([],url);
					// files.push({
					// 	"file" : test,
					// 	"data" : url,
					// })
					// var img = "<img src=" + url + " /> ";
					// $( "#' . $div_id . '" ).append($("<div>").append(img + "<span>" + test.name + "</span>"));
					// $("#' . $div_id . '_description").remove();
					var description = $("#' . $div_id . '_description");

					$(this).on({
						"dragover dragenter": function(e) {
							e.preventDefault();
							e.stopPropagation();
						},
						"drop": function(e) {
							e.preventDefault();
							e.stopPropagation();
						}
					})

					// Remove File
					function file_upload_remove(e) {
						$( ".' . $div_id . '_remove" ).click(function(e) {
							e.preventDefault();
							e.stopPropagation();
							var index = files.findIndex(function(value) {
								return (value.data == this);
							}, $(this).attr("data"))
							if (index > -1) {
								files.splice(index, 1);
								$(this).parent().remove();
							}
							if (files.length == 0) {
								$("#' . $div_id . '").append(description);
							}
						})
					}

					// Add Image by Drag & Drop
					$( "#' . $div_id . '" ).on({
						"drop": function(e) {
							$( "#' . $div_id . '" ).sortable();
							var dataTransfer = e.originalEvent.dataTransfer;
							if( dataTransfer && dataTransfer.files.length) {
								e.preventDefault();
								e.stopPropagation();
								$("#' . $div_id . '_description").remove();
								$.each( dataTransfer.files, function(i, file) {
									var reader = new FileReader();
									reader.onload = $.proxy(function(file, fileList, event) {
										var check = files.some(function(value) {
											return (value.data == this);
										}, event.target.result);
										if (check) {
											$("#' . $div_id . '_message").empty();
											$("#' . $div_id . '_message").append("Cant Add Same File");
											$("#' . $div_id . '_message").show(500).delay(2000).hide(500);
										} else {
											files.push({
												"file" : file,
												"data" : event.target.result
											});
											var img = file.type.match("image.*") ? "<img src=" + event.target.result + " /> " : "";');
				if ($this->removeLink) {
					$js->appendln('
											fileList.append($("<div>").append(img + "<span>" + file.name + "</span>").append("<a class=' . $div_id . '_remove data=" + event.target.result + ">Remove</a>"));
											file_upload_remove();
					');
				} else {
					$js->appendln('
											fileList.append($("<div>").append(img + "<span>" + file.name + "</span>"));
					');
				}
				$js->appendln('
										}
									}, this, file, $("#' . $div_id . '"));
									reader.readAsDataURL(file);
								});
							}
						}
					})

					// Add Image by Click
					$( "#' . $div_id . '" ).click(function() {
						$( "#' . $div_id . '_input" ).trigger("click");
					})
					$( "#' . $div_id . '_input" ).change(function(e) {
						$("#' . $div_id . '_description").remove();
						$.each(e.target.files, function(i, file) {
							var reader = new FileReader();
							reader.onload = $.proxy(function(file, fileList, event) {
								var check = files.some(function(value) {
									return (value.data == this);
								}, event.target.result);
								if (check) {
									$("#' . $div_id . '_message").empty();
									$("#' . $div_id . '_message").append("Cant Add Same File");
									$("#' . $div_id . '_message").show(500).delay(2000).hide(500);
								} else {
									files.push({
										"file" : file,
										"data" : event.target.result
									});
									var img = file.type.match("image.*") ? "<img src=" + event.target.result + " /> " : "";');
				if ($this->removeLink) {
					$js->appendln('
									fileList.append($("<div>").append(img + "<span>" + file.name + "</span>").append("<a class=' . $div_id . '_remove data=" + event.target.result + ">Remove</a>"));
									file_upload_remove();
					');
				} else {
					$js->appendln('
									fileList.append($("<div>").append(img + "<span>" + file.name + "</span>"));
					');
				}
				$js->appendln('
								}
							}, this, file, $("#' . $div_id . '"));
							reader.readAsDataURL(file);
						})
						$(this).val("");
					})

					// Handler Submit Data
					$( "#' . $div_id . '" ).parents("form").submit(function(e) {
						e.preventDefault();
						e.stopPropagation();
						var data = new FormData(this);
						$.each($("#'.$div_id.'").children(), function(i, file) {
							var index = files.findIndex(function(value) {
								return (value.data == this);
							}, file.firstChild.src)
							data.append("' . $this->id . '[]", files[index].file);
						})

						var xhr = new XMLHttpRequest();
						xhr.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								document.open("text/html", "replace");
								document.write(xhr.responseText);
								document.close();
							}
						}
						xhr.open("post", e.target.action);
						xhr.send(data);
					});
				');
			}
			return $js->text();
		}

	}
