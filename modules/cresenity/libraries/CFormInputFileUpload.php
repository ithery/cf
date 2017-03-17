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
                        border: 2px dashed #CDCDCD;
                    }
                    #' . $div_id . '.ondrag {
                        border: 5px dashed #CDCDCD;
                        background-color: rgba(0, 0, 0, 0.05);
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
                    .' . $div_id . '_file.loading .' . $div_id . '_loading {
                        display: block;
                    }
                    .' . $div_id . '_file .' . $div_id . '_loading {
                        display: none;
                    }
                    .' . $div_id . '_loading {
                        position: absolute;
                        top: 0;
                        left: 0;
                        z-index: 1000;
                        background-color: rgba(0, 0, 0, 0.5);
                    }
                    .' . $div_id . '_remove {
                        cursor: pointer;
                    }
                </style>
                <input id="' . $div_id . '_input_temp" type="file" name="'.$div_id.'_input_temp[]" multiple style="display:none;">
                <div id="' . $div_id . '_message" class="row alert alert-danger fade in">
                </div>
                <div id="' . $div_id . '" class="row control-fileupload">
                ');
        
        foreach($this->files as $f) {
            $input_name = carr::get($f,'input_name');
            $file_url = carr::get($f,'file_url');
                    //<input id="' . $div_id . '_input_'.$ii.'" class="'.$div_id.'_i" type="file" name="'.$this->name.'['.$input_name.']" style="display:none;">    
            $html->appendln('
                <div class="' . $div_id . '_file">
                    <img src="'.$file_url.'" />
                    <a class="' . $div_id . '_remove">Remove</a>
                    <input type="hidden" name="'. $this->name .'['.$input_name.']" value="">
                </div>
            ');
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
            
            $js->appendln('
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
                       
                        
                        $(this).parent().remove();
                       
                        if ($("#' . $div_id . '").children().length==0) {
                                $("#' . $div_id . '").append("<span id=\"' . $div_id . '_description\">Click Here or Drop Files Here</span>");
                        }
                    })
                }

                file_upload_remove();
                $( "#' . $div_id . '" ).sortable();

                // Add Image by Drag & Drop
                $( "#' . $div_id . '" ).on({
                    "dragover dragenter": function(e) {
                        $(this).addClass("ondrag");
                    },
                    "dragleave dragend": function(e) {
                        $(this).removeClass("ondrag");
                    },
                    "drop": function(e) {
                        $(this).removeClass("ondrag");
                        $( "#' . $div_id . '" ).sortable();
                        var dataTransfer = e.originalEvent.dataTransfer;
                        if( dataTransfer && dataTransfer.files.length) {
                            e.preventDefault();
                            e.stopPropagation();
                            $("#' . $div_id . '_description").remove();
                            $.each( dataTransfer.files, function(i, file) {
                                var reader = new FileReader();
                                reader.onload = $.proxy(function(file, fileList, event) {
                                    
                                    var img = file.type.match("image.*") ? "<img src=" + event.target.result + " /> " : "";
                                    var div = $("<div>").addClass("' . $div_id . '_file");
                                    div.append(img);
            ');
            if ($this->removeLink) {
                $js->appendln('
                                    var remove = $("<a>").addClass("' . $div_id . '_remove").html("Remove");
                                    div.append(remove);
                ');
            }
            $js->appendln('
                                    div.append("<img class=\"' . $div_id . '_loading\" src=\"'.curl::base().'media/img/ring.gif\" />");
                                    fileList.append(div.addClass("loading"));
                                    file_upload_remove();

                                    var data = new FormData();
                                    data.append("' . $this->name . '[]", file);
                                    var xhr = new XMLHttpRequest();
                                    xhr.onreadystatechange = function() {
                                        if (this.readyState == 4 && this.status == 200) {
                                            div.removeClass("loading");
                                            div.append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">");
                                        } else if (this.readyState == 4 && this.status != 200) {
                                            //div.remove();
                                        }
                                    };
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
                    $( "#' . $div_id . '_input_temp" ).trigger("click");
                })
                $( "#' . $div_id . '_input_temp" ).change(function(e) {
                    $("#' . $div_id . '_description").remove();
                    $.each(e.target.files, function(i, file) {
                        var reader = new FileReader();
                        reader.onload = $.proxy(function(file, fileList, event) {
                                
                            var img = file.type.match("image.*") ? "<img src=" + event.target.result + " /> " : "";
                            var div = $("<div>").addClass("' . $div_id . '_file");
                            div.append(img);
            ');
            if ($this->removeLink) {
                $js->appendln('
                            var remove = $("<a>").addClass("' . $div_id . '_remove").html("Remove");
                            div.append(remove);
                ');
            }
            $js->appendln('
                            div.append("<img class=\"' . $div_id . '_loading\" src=\"'.curl::base().'media/img/ring.gif\" />");
                            fileList.append(div.addClass("loading"));
                            file_upload_remove();

                            var data = new FormData();
                            data.append("' . $this->name . '[]", file);
                            var xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    div.removeClass("loading");
                                    div.append("<input type=\"hidden\" name=\"'. $this->name .'[]\" value="+ this.responseText +">");
                                } else if (this.readyState == 4 && this.status != 200) {
                                    //div.remove();
                                }
                            };
                            xhr.open("post", "'.$ajax_url.'");
                            xhr.send(data);
                        }, this, file, $("#' . $div_id . '"));
                        reader.readAsDataURL(file);
                    })
                    $(this).val("");
                })        
            ');
        }
        return $js->text();
    }

}
