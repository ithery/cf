<?php

class CFormInputFileUpload extends CFormInput {

    protected $applyjs;
    protected $uniqid;
    protected $removeLink;
    protected $disabled;
    protected $files;
    protected $link;

    public function __construct($id) {
        parent::__construct($id);

        $this->removeLink = 'true';
        $this->type = "fileupload";
        $this->applyjs = "fileupload";
        $this->uniqid = uniqid();
        $this->files = array();
        $this->link = '';
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

    public function add_file($file_url,$input_name="") {
        $arr = array();
        $arr['input_name'] = $input_name;
        $arr['file_url'] = $file_url;
        
        $this->files[] = $arr;
        return $this;
    }

    public function set_link($value) {
        $this->link = $value;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $div_id = 'div_fileupload_' . $this->id;
        $html->appendln('
                <style>
                        #' . $div_id . ' {
                                margin-left: 0px;
                                padding: 10px 10px;
                                border: 1px dashed #CDCDCD;
                        }
                        #' . $div_id . ' div {
                                border: 1px solid #ddd;
                                margin: 10px 10px;
                                width: 100px;
                                height: 100px;
                                float: left;
                                text-align: center;
                                position: relative;
                                border-radius: 4px;
                                padding:4px;
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
                <input id="' . $div_id . '_input" type="file" name="'.$div_id.'_input_temp[]" multiple style="display:none;">
                <div id="' . $div_id . '_message" class="row alert alert-danger fade in">
                </div>
                <div id="' . $div_id . '" class="row">
                ');
        
        $ii=0;
        foreach($this->files as $f) {
            $input_name = carr::get($f,'input_name');
            $file_url = carr::get($f,'file_url');
                    //<input id="' . $div_id . '_input_'.$ii.'" class="'.$div_id.'_i" type="file" name="'.$this->name.'['.$input_name.']" style="display:none;">    
            $html->appendln('
                <div>
                    <img data="'.$ii.'" src="'.$file_url.'" />
                    <a class="' . $div_id . '_remove" data="'.$ii.'">Remove</a>
                    <input type="hidden" name="'. $this->name .'['.$input_name.']" value="">
                </div>
                    
            ');
            $ii++;
        }
        
        $html_description = '';
        if(count($this->files)==0) {
            $html_description = '<span id="' . $div_id . '_description">Click Here or Drop Files Here</span>';
        }
        $html->appendln(        '
                        '.$html_description.'
                </div>
        ');
        return $html->text();
    }

    public function js($indent = 0) {
        $ajax_url = CAjaxMethod::factory()->set_type('fileupload')
                        ->set_data('input_name', $this->name)
                        ->makeurl();
        
        $div_id = 'div_fileupload_' . $this->id;
        $js = new CStringBuilder();
        $js->set_indent($indent);
        if (empty($ajax_url)) {
            throw new Exception('Link is empty', 1);
        }
        if ($this->applyjs == "fileupload") {
            $arr_files = array();
            $ii=0;
            foreach($this->files as $f) {
                $input_name = carr::get($f,'input_name');
                $file_url = carr::get($f,'file_url');
                $arr=[];
                $arr['file']='';
                $arr['data']=$input_name;
                $arr['id']=$ii;
                $arr_files[] = $arr;
                $ii++;
            }
            
            $file_json = json_encode($arr_files);
            $js->appendln('
                window.files = '.$file_json.';
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
                                        return (value.id == this);
                                }, $(this).attr("data"))
                                
                                if (index > -1) {
                                        files.splice(index, 1);
                                        $(this).parent().remove();
                                }
                                if (files.length == 0) {
                                        $("#' . $div_id . '").append("<span id=\"' . $div_id . '_description\">Click Here or Drop Files Here</span>");
                                }
                        })
                }
                file_upload_remove();
                $( "#' . $div_id . '" ).sortable();
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
                                                    files.push({
                                                            "file" : file,
                                                            "data" : event.target.result,
                                                            "id" : files.length
                                                    });
                                                    var img = file.type.match("image.*") ? "<img data="+ (files.length-1) +" src=" + event.target.result + " /> " : "";

                                                    var data = new FormData();
                                                    data.append("' . $this->name . '[]", file);
                                                    var xhr = new XMLHttpRequest();
            ');

            if ($this->removeLink) {
                $js->appendln('
                                                    xhr.onreadystatechange = function() {
                                                        if (this.readyState == 4 && this.status == 200) {
                                                            var index = files.findIndex(function(value) {
                                                                    return (value.file == this);
                                                            }, file)
                                                            fileList.append($("<div>").append(img + "").append("<a class=\"' . $div_id . '_remove\" data=" + index + ">Remove</a>").append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">"));
                                                            file_upload_remove();
                                                        }
                                                    };
                ');
            } else {
                $js->appendln('
                                                    fileList.append($("<div>").append(img).append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">"));
                ');
            }
            $js->appendln('
                                                    xhr.open("post", "'.$ajax_url.'");
                                                    xhr.send(data);
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
                                            files.push({
                                                    "file" : file,
                                                    "data" : event.target.result,
                                                    "id" : files.length
                                            });
                                            var img = file.type.match("image.*") ? "<img data="+ (files.length-1) +" src=" + event.target.result + " /> " : "";

                                            var data = new FormData();
                                            data.append("' . $this->name . '[]", file);
                                            var xhr = new XMLHttpRequest();
            ');

            if ($this->removeLink) {
                $js->appendln('
                                            xhr.onreadystatechange = function() {
                                                if (this.readyState == 4 && this.status == 200) {
                                                    var index = files.findIndex(function(value) {
                                                            return (value.file == this);
                                                    }, file)
                                                    fileList.append($("<div>").append(img + "").append("<a class=\"' . $div_id . '_remove\" data=" + (files.length-1) + ">Remove</a>").append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">"));
                                                    file_upload_remove();
                                                }
                                            };
					');
            } else {
                $js->appendln('
                                            fileList.append($("<div>").append(img).append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">"));
					');
            }
            $js->appendln('
                                            xhr.open("post", "'.$ajax_url.'");
                                            xhr.send(data);
                                            }, this, file, $("#' . $div_id . '"));
                                            reader.readAsDataURL(file);
                                        })
                                        $(this).val("");
                                })

                                // Handler Submit Data
                                // $( "#' . $div_id . '" ).parents("form").submit(function(e) {
                                //         e.preventDefault();
                                //         e.stopPropagation();
                                //         var data = new FormData(this);
                                //         $.each($("#' . $div_id . '").children(), function(i, file) {
                                //                 var index = files.findIndex(function(value) {
                                                        
                                //                         return (value.id == this);
                                //                 }, $(file).children().first().attr("data"))
                                //                 if(files[index].file=="") {
                                //                     data.append("' . $this->name . '[]", new File([""], files[index].data));
                                //                 } else {
                                //                     data.append("' . $this->name . '[]", files[index].file);
                                //                 }
                                //         })
                                        
                                //         var xhr = new XMLHttpRequest();
                                //         xhr.onreadystatechange = function() {
                                //                 if (this.readyState == 4 && this.status == 200) {
                                //                         document.open("text/html", "replace");
                                //                         document.write(xhr.responseText);
                                //                         document.close();
                                //                 }
                                //         }
                                //         xhr.open("post", e.target.action);
                                //         xhr.send(data);
                                // });
            ');
        }
        return $js->text();
    }

}
