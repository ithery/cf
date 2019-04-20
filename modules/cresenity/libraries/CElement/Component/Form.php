<?php

class CElement_Component_Form extends CElement_Component {

    use CTrait_Compat_Element_Form;

    protected $name;
    protected $method;
    protected $autocomplete;
    protected $layout;
    protected $action;
    protected $target;
    protected $enctype;

    /**
     *
     * @var CElement_Component_Form_Validation
     */
    protected $validation;
    protected $remoteValidationUrl;
    protected $trigger_submit;
    protected $ajax_submit;
    protected $ajax_success_script_callback;
    protected $ajax_datatype;
    protected $ajax_redirect;
    protected $ajax_process_progress;
    protected $ajax_process_progress_cancel;
    protected $ajax_upload_progress;
    protected $ajax_redirect_url;
    protected $ajax_process_id;
    protected $ajax_submit_handlers;
    protected $ajax_submit_target;
    protected $ajax_submit_target_class;
    protected $auto_set_focus;
    protected $action_before_submit;
    protected $disable_js;
    protected $submitListener;

    public function __construct($form_id = "") {
        parent::__construct($form_id);
        $this->tag = 'form';

        $this->name = $this->id;
        $this->method = "POST";
        $this->target = "_self";
        $this->layout = "horizontal";
        $this->action = "";
        $this->autocomplete = true;
        $this->enctype = "application/x-www-form-urlencoded";
        $this->validation = true;
        $this->trigger_submit = array();
        $this->ajax_submit = false;
        $this->ajax_success_script_callback = "";
        $this->ajax_datatype = "text";
        $this->ajax_upload_progress = false;
        $this->ajax_process_progress = false;
        $this->ajax_process_progress_cancel = false;
        $this->ajax_process_id = cutils::randmd5();
        $this->ajax_redirect = true;
        $this->ajax_redirect_url = "";
        $this->ajax_submit_handlers = array();
        $this->ajax_submit_target = false;
        $this->ajax_submit_target_class = false;
        $this->auto_set_focus = true;
        $this->action_before_submit = '';
        $this->disable_js = false;

        if ($this->bootstrap == '3.3') {
            $this->layout = carr::get($this->theme_style, 'form_layout');
        }
        CManager::instance()->register_module('validation');
    }

    public static function factory($id = "") {
        return new CElement_Component_Form($id);
    }

    public function onSubmitListener() {
        return $this->addListener('submit');
    }
    
    /**
     * 
     * @param string $event
     * @return CObservable_Listener
     */
    public function addListener($event) {
        if ($event != 'submit') {
            return parent::addListener($event);
        }
        if ($this->submitListener == null) {
            $this->submitListener = new CObservable_Listener($this->id, $event);
        }
        return $this->submitListener;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Set action attribute value of form element
     * 
     * @param string $action action attribute of form
     * @return CElement_Component_Form
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * Set method attribute value of form element
     * 
     * @param string $method POST|GET|PUT|DELETE
     * @return CElement_Component_Form
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * Set target attribute value of form element
     * 
     * @param string $target target attribute of form
     * @return CElement_Component_Form
     */
    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }

    /**
     * Set enctype attribute value of form element
     * 
     * @param string $method multipart/form-data|
     * @return CElement_Component_Form
     */
    public function setEncType($encType = 'multipart/form-data') {
        $this->enctype = $encType;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setAutoComplete($bool) {
        $this->autocomplete = $bool;
        return $this;
    }

    /**
     * 
     * @param mixed $validation
     * @return $this
     */
    public function setValidation($validationData = true) {


        if (is_array($validationData)) {
            CManager::asset()->module()->registerRunTimeModules('validate');
            $this->validation = new CElement_Component_Form_Validation($validationData);

            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('Validation');
            $ajaxMethod->setData('dataValidation', $validationData);
            $ajaxMethod->setData('formId', $this->id());
            $ajaxUrl = $ajaxMethod->makeUrl();

            $this->remoteValidationUrl = $ajaxUrl;
            return $this;
        }


        $this->validation = $validationData;
        return $this;
    }

    /**
     * Make form to submit through ajax
     * 
     * @param string $bool
     * @return CElement_Component_Form
     */
    public function setAjaxSubmit($bool = true) {
        $this->ajax_submit = $bool;
        return $this;
    }

    public function set_ajax_datatype($datatype) {
        $this->ajax_datatype = $datatype;
        return $this;
    }

    public function setAjaxSubmitTarget($target) {
        $this->ajax_submit_target = $target;
        return $this;
    }

    public function set_disable_js($bool) {
        $this->disable_js = $bool;
        return $this;
    }

    public function set_ajax_submit_target_class($target) {
        $this->ajax_submit_target_class = $target;
        return $this;
    }

    public function set_ajax_success_script_callback($jsfunc) {
        $this->ajax_success_script_callback = $jsfunc;
        return $this;
    }

    public function setAjaxRedirect($bool) {
        $this->ajax_redirect = $bool;
        return $this;
    }

    public function set_ajax_upload_progress($bool) {
        $this->ajax_upload_progress = $bool;
        return $this;
    }

    public function set_ajax_process_progress($bool) {
        $this->ajax_process_progress = $bool;
        return $this;
    }

    public function set_ajax_process_progress_cancel($bool) {
        $this->ajax_process_progress_cancel = $bool;
        return $this;
    }

    public function set_ajax_redirect_url($url) {
        $this->ajax_redirect_url = $url;
        return $this;
    }

    public function trigger_submit($elem, $evt) {
        $this->trigger_submit[] = CJSTrigger::factory($elem, $evt);
        return $this;
    }

    public function set_action_before_submit($action_before_submit) {
        $this->action_before_submit = $action_before_submit;
        return $this;
    }

    public function set_auto_set_focus($bol) {
        $this->auto_set_focus = $bol;
        return $this;
    }

    /**
     *
     * @param string $handler_name
     * @return CHandler
     */
    public function add_ajax_submit_handler($handler_name) {
        $handler = CHandler::factory($this->id, 'submit', $handler_name);
        $this->ajax_submit_handlers[] = $handler;
        return $handler;
    }

    public function toarray($indent = 0) {
        $data = array();
        $data = array_merge_recursive($data, parent::toarray());

        if (strlen($this->action) > 0) {
            $data['attr']['action'] = $this->action;
        }
        if (strlen($this->method) > 0) {
            $data['attr']['method'] = $this->method;
        }
        return $data;
    }

    public function build() {
        if ($this->autocomplete) {
            $this->setAttr('autocomplete', 'on');
        } else {
            $this->setAttr('autocomplete', 'off');
        }
        if (strlen($this->enctype) > 0) {
            $this->setAttr('enctype', $this->enctype);
        }
        if (strlen($this->name) > 0) {
            $this->setAttr('name', $this->name);
        }
        if (strlen($this->target) > 0) {
            $this->setAttr('target', $this->target);
        }
        if (strlen($this->method) > 0) {
            $this->setAttr('method', $this->method);
        }
        if (strlen($this->action) > 0) {
            $this->setAttr('action', $this->action);
        }
        if (strlen($this->layout) > 0) {
            $this->addClass('form-' . $this->layout);
        }
        if ($this->ajax_process_progress) {
            $this->add('<input type="hidden" id="cprocess_id" name="cprocess_id" value="' . $this->ajax_process_id . '">');
        }
    }

    public function js($indent = 0) {
        if ($this->disable_js) {
            return parent::js($indent);
        }


        $js = new CStringBuilder();
        $js->setIndent($indent);
        if ($this->validation instanceof CElement_Component_Form_Validation) {
            $js->append($this->validation->validator()->selector('#' . $this->id()));
        }

        $jsSubmitHandlers = '';
        $jsSubmitReturn = 'return true;';
        if ($this->submitListener != null) {
            foreach ($this->submitListener->handlers() as $handler) {
                $jsSubmitHandlers .= $handler->js();
                $jsSubmitReturn = 'return false;';
            }
        }
        if ($this->ajax_submit) {
            $ajax_url = "";
            $ajax_process_script = "";
            $ajax_process_done_script = "";
            if ($this->ajax_process_progress) {

                $ajax_process_url = CAjaxMethod::factory()->set_type('form_process')
                        ->set_data('form', serialize($this))->set_method('POST')
                        ->makeurl();
                $ajax_process_script_buttons = "	buttons: {}, ";
                if ($this->ajax_process_progress_cancel) {
                    $ajax_process_script_buttons = "
                        'buttons': {
                                'Cancel': {
                                        'primary': true,
                                        'click': function() {
                                                jQuery.ajax('" . $ajax_process_url . "', {
                                                        dataType: 'json',
                                                        type: 'POST',
                                                        data : {ajax_process_id:'" . $this->ajax_process_id . "',cancel:true},
                                                        success: function(data) {
                                                                $(this).dialog2('close');
                                                        },
                                                        error: function(xhr, status, error) {
                                                                $.cresenity.message('error','[AJAX PROCESS] ' + status + ' - Server reponse is: ' + xhr.responseText);
                                                        }
                                                });

                                        }
                                }
                        },
                    ";
                }
                $ajax_process_script = "
                    if(cprocess_run_once_" . $this->id . "==false) {
                           cprocess_run_once" . $this->id . "=true;
                           ctimer_" . $this->id . " = setInterval(function()  {

                                   jQuery.ajax('" . $ajax_process_url . "', {
                                           dataType: 'json',
                                           type: 'POST',
                                           data : {ajax_process_id:'" . $this->ajax_process_id . "'},
                                           success: function(data) {
                                                   var percentComplete = data.percent;
                                                   var info = data.info;
                                                   if(info=='undefined') info = '';
                                                   //update the progress bar
                                                   //do create progress
                                                   var progress = $('#progress_" . $this->id . "');
                                                   if(progress.length==0) {
                                                           progress = $('<div id=\"progress_" . $this->id . "\"class=\"progress progress-striped active\"><div id=\"bar_" . $this->id . "\" class=\"bar\" style=\"width: 0%;\"><p>0%</p></div></div>');
                                                           var span = $('<div class=\"span12\">');
                                                           var span_info = $('<div class=\"span12\" id=\"info_" . $this->id . "\">');
                                                           var div = $('<div class=\"row-fluid\" style=\"width:auto\">');
                                                           div.append(span);
                                                           div.append(span_info);
                                                           span.append(progress);
                                                           $.cresenity.dialog.show(div,{
                                                                   " . $ajax_process_script_buttons . "
                                                                   showCloseHandle: false,
                                                                   closeOnEscape: false,
                                                                   closeOnOverlayClick: false,
                                                                   title: 'Progress',
                                                                   autoOpen: true,
                                                                   removeOnClose: true
                                                           });

                                                   };
                                                   var percentVal = percentComplete + '%';
                                                   $('#bar_" . $this->id . "').width(percentVal);
                                                   $('#bar_" . $this->id . "').find('p').html(percentVal);
                                                   $('#info_" . $this->id . "').html(info);
                                                   if(percentComplete==100) {
                                                           $('#bar_" . $this->id . "').parent().parent().parent().remove();
                                                   }
                                           },
                                           error: function(xhr, status, error) {
                                                   $.cresenity.message('error','[AJAX PROCESS] ' + status + ' - Server reponse is: ' + xhr.responseText);
                                           }
                                   });
                           },2000);
                   }
                ";
                $ajax_process_done_script = "
                    clearInterval(ctimer_" . $this->id . ");
                    var progress = $('#progress_" . $this->id . "');
                    if(progress.length>0) {
                        modal = progress.closest('.modal');
                        if(modal.length>0) {
                            modal.remove();
                            $('.modal-backdrop').remove();
                        }
                    }
                ";
            }
            $redirect_url = $this->ajax_redirect_url;
            $ajax_url = $this->action;
            if (strlen($redirect_url) == 0) {
                //ajax to this page
                $ajax_url = curl::base() . crouter::complete_uri();
            }
            if (strlen($redirect_url) == 0) {
                //redirect to this page
                $redirect_url = curl::base() . crouter::complete_uri();
            }
            $script_redirect_url = '';
            if ($this->ajax_redirect) {
                $script_redirect_url = "document.location.href = '" . $redirect_url . "';";
            }
            $script_callback = '';
            if ($this->ajax_redirect) {
                
            }
            if (count($this->ajax_submit_handlers) > 0) {
                $script_redirect_url = "";
                foreach ($this->ajax_submit_handlers as $handler) {
                    $script_redirect_url .= $handler->js();
                }
            }
            $upload_progress_before_submit = "";
            $upload_progress_success = "";
            $upload_progress_upload = "";
            if ($this->ajax_upload_progress) {
                $upload_progress_before_submit = "
					var progress = $('#" . $this->id . "').find('#progress_" . $this->id . "');
					if(progress.length==0) {
						//find progress first
						var progress = $('#progress_" . $this->id . "');
						if(progress.length==0) {
							//do create progress
							progress = $('<div id=\"progress_" . $this->id . "\"class=\"progress progress-striped active\"><div id=\"bar_" . $this->id . "\" class=\"bar\" style=\"width: 0%;\"><p>0%</p></div></div>');
							var span = $('<div class=\"span12\">');
							var span_info = $('<div class=\"span12\" id=\"info_" . $this->id . "\">');
							var div = $('<div class=\"row-fluid\" style=\"width:auto\">');
							div.append(span);
							div.append(span_info);
							span.append(progress);
							$.cresenity.dialog.show(div,{
								showCloseHandle: false,
								closeOnEscape: false,
								closeOnOverlayClick: false,
								title: 'Uploading',
								autoOpen: true,
								buttons: {},
								removeOnClose: true
							});

						};



					} else {
						var percentVal = '0%';
						$('#bar_" . $this->id . "').width(percentVal);
						$('#bar_" . $this->id . "').find('p').html(percentVal);
						$('#info_" . $this->id . "').html('&nbsp;');
					}
				";
                $upload_progress_upload = "
					var percentVal = percentComplete + '%';
					$('#bar_" . $this->id . "').width(percentVal);
					$('#bar_" . $this->id . "').find('p').html(percentVal);
					$('#info_" . $this->id . "').html('Uploading '+position+'/'+total+'<br/>'+percentVal+' Completed');
					if(percentComplete==100) {
						$('#bar_" . $this->id . "').parent().parent().parent().remove();
						" . $ajax_process_script . "
					}
				";
                $upload_progress_success = "
					var percentVal = '100%';
					$('#bar_" . $this->id . "').width(percentVal);
					$('#bar_" . $this->id . "').find('p').html(percentVal);
					$('#info_" . $this->id . "').html('&nbsp;');
				";
            }

            $js->appendln("
				var cprocess_run_once_" . $this->id . " = false;
				var ctimer_" . $this->id . " = false;
			");
            $ajax_process_without_upload = $ajax_process_script;
            if ($this->ajax_upload_progress) {
                $ajax_process_without_upload = "";
            }

            $on_success_script = "
				$('#" . $this->id . "').removeClass('loading');
				$('#" . $this->id . "').find('*').removeClass('disabled');


				if(typeof data=='object') {
					var result = data.result;
					var message = data.message;
					if(result=='OK'||result=='1'||result===true) {
						$.cresenity.message('success',message);
						" . $script_redirect_url . "
					} else {
						$.cresenity.message('error',message);
					}
				} else if(typeof data== 'string') {
					if(data.toLowerCase().substring(0,5) != 'error') {
						if(data!='') {
							$.cresenity.message('success',data);
						} else {
							//do nothing
							//the message must be set on session
						}
						" . $script_redirect_url . "
					} else {
						$.cresenity.message('error',data);
					}


				} else {
					$.cresenity.message('success',data);
					" . $script_redirect_url . "
				}
			";
            $on_before_submit = "";
            if (strlen($this->ajax_submit_target) > 0) {
                if ($this->bootstrap >= '3.3') {
                    $on_before_submit = "
                                jQuery('#" . $this->ajax_submit_target . "').children().hide();
                                jQuery('#" . $this->ajax_submit_target . "').append(jQuery('<div>').attr('id','#" . $this->ajax_submit_target . "-loading').addClass('loading'));
                            ";
                } else {
                    $on_before_submit = "
                                jQuery('#" . $this->ajax_submit_target . "').children().hide();
                                jQuery('#" . $this->ajax_submit_target . "').append(jQuery('<div>').attr('id','#" . $this->ajax_submit_target . "-loading').css('text-align','center').css('margin-top','100px').css('margin-bottom','100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))
                            ";
                }

                $this->ajax_datatype = "json";
                //the response is json
                $on_success_script = "
				jQuery('#" . $this->ajax_submit_target . "').html(data.html);
				var script = $.cresenity.base64.decode(data.js);

				eval(script);
				jQuery('#" . $this->ajax_submit_target . "').removeClass('loading');
				jQuery('#" . $this->ajax_submit_target . "').data('xhr',false);
				if(jQuery('#" . $this->ajax_submit_target . "').find('.prettyprint').length>0) {
					window.prettyPrint && prettyPrint();
				}
				";
            }

            if (strlen($this->ajax_submit_target_class) > 0) {
                $on_before_submit = "
					jQuery('." . $this->ajax_submit_target_class . "').children().hide();
					jQuery('." . $this->ajax_submit_target_class . "').append(jQuery('<div>').attr('class','." . $this->ajax_submit_target_class . "-loading').css('text-align','center').css('margin-top','100px').css('margin-bottom','100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))

				";

                $this->ajax_datatype = "json";
                //the response is json
                $on_success_script = "
				jQuery('." . $this->ajax_submit_target_class . "').html(data.html);
				var script = $.cresenity.base64.decode(data.js);

				eval(script);
				jQuery('." . $this->ajax_submit_target_class . "').removeClass('loading');
				jQuery('." . $this->ajax_submit_target_class . "').data('xhr',false);
				if(jQuery('." . $this->ajax_submit_target_class . "').find('.prettyprint').length>0) {
					window.prettyPrint && prettyPrint();
				}
				";
            }

            $validation_if_open = '';
            $validation_if_close = '';

            if ($this->validation) {
                $validation_if_open = "if ($('#" . $this->id . "').validationEngine('validate') ) {";
                $validation_if_close = "					} else {
						$('#" . $this->id . " .confirm').removeAttr('data-submitted');
					}
                ";
            }
            $js->appendln("
				$('#" . $this->id . " input[type=submit]').click(function() {
					$('input[type=submit]', $(this).parents('form')).removeAttr('clicked');
					$(this).attr('clicked', 'true');
				});
				$('#" . $this->id . "').submit(function(event) {

                    var fileupload = $('.control-fileupload');
                    var error = 0;
                    fileupload.each(function() {
                        $(this).children().each(function() {
                            if($(this).hasClass('loading')) {
                                alert('There\'s any file still uploading');
                                error++;
                            }
                        })
                    });
                    if($('#" . $this->id . " .fileupload-preview.loading').length>0) {
                        alert('There\'s any file still uploading');
                        error++;
                    }
                    if($('#" . $this->id . " .multi-image-ajax-file.loading').length>0) {
                        alert('There\'s any file still uploading');
                        error++;
                    }

                    if (error > 0) {
                        $('#" . $this->id . " .confirm').removeAttr('data-submitted');
                        return false;
                    }     

					" . $validation_if_open . "
						if($('#" . $this->id . "').hasClass('loading')) return false;
						//don't do it again if still loading
						$('#" . $this->id . "').addClass('loading');
						$('#" . $this->id . "').find('*').addClass('disabled');
						" . $ajax_process_without_upload . "



						var form_ajax_url = $('#" . $this->id . "').attr('action');
						if(!form_ajax_url) form_ajax_url = '" . $ajax_url . "';
						var options = {
							url: form_ajax_url,
							dataType: '" . $this->ajax_datatype . "',
							type: '" . $this->method . "',
							beforeSubmit: function(arr, form, options) {
								" . $upload_progress_before_submit . "
							},
							uploadProgress: function(event, position, total, percentComplete) {
								" . $upload_progress_upload . "
								//console.log(percentVal, position, total);
							},
							success: function(data) {
                                                            $.cresenity._handle_response(data,function() {
                                                                    $('#" . $this->id . "').find('*').removeClass('disabled');
                                                                    $('#" . $this->id . "').removeClass('loading');
                                                                    " . $upload_progress_success . "
                                                                    " . $ajax_process_done_script . "
                                                                    " . $on_success_script . "
                                                                });
                                                                //do callback


							},

							error: function(xhr, status, error) {
								$('#" . $this->id . "').find('*').removeClass('disabled');
								" . $ajax_process_done_script . "
								$('#" . $this->id . "').removeClass('loading');
								$.cresenity.message('error','[AJAX] ' + status + ' - Server reponse is: ' + xhr.responseText);
								$('#" . $this->id . "').find('*').removeClass('disabled');
							}
						}
						" . $on_before_submit . " 
						" . $this->action_before_submit . "
						$('#" . $this->id . "').ajaxSubmit(options); 
					" . $validation_if_close . "	
					//never submit form
					return false;
				});
			");
        } else {
            $js->appendln("//Form validation")->br();
            $strvalidation = "";
            if ($this->validation) {
                $strvalidation = "$('#" . $this->id . "').validationEngine();";
            }

            $js->appendln("
                    $('#" . $this->id . "').submit(function(event) {

                        var fileupload = $('.control-fileupload');
                        var error = 0;
                        fileupload.each(function() {
                            $(this).children().each(function() {
                                if($(this).hasClass('loading')) {
                                    alert('There\'s any file still uploading');
                                    error++;
                                }
                            })
                        });
                        if($('#" . $this->id . " .fileupload-preview.loading').length>0) {
                            alert('There\'s any file still uploading');
                            error++;
                        }
                        if($('#" . $this->id . " .multi-image-ajax-file.loading').length>0) {
                            alert('There\'s any file still uploading');
                            error++;
                        }
                        if (error > 0) {
                            $('#" . $this->id . " .confirm').removeAttr('data-submitted');
                            return false;
                        }
                        

                        " . $jsSubmitHandlers . "
                        " . $jsSubmitReturn . "
                    });
                ");

            $js->appendln("
                " . $strvalidation . "
                $('#" . $this->id . "').bind('jqv.form.result', function(event , errorFound){
                    if(errorFound) {
                            $('#" . $this->id . " .confirm').removeAttr('data-submitted');
                    }
                    else {")->br();
            if (strlen($this->action_before_submit) > 0) {
                $js->appendln($this->action_before_submit);
            }
            $js->appendln("
                        }
                    });
                ")->br();
        }
        if (count($this->trigger_submit) > 0) {
            foreach ($this->trigger_submit as $t) {
                $field = $this->get_field_by_id($t->element_id);
                if ($field == null) {
                    trigger_error('There are no element id "' . $t->element_id . '" on this form for trigger submit', E_USER_WARNING);
                }
                if ($field != null) {
                    //opening
                    if ($t->event == "change" && $field->field_type == "select") {
                        $js->appendln("var select = jQuery('#" . $t->element_id . "').data('replacement');")->br();
                        $js->appendln("select.on('select-close',function() {")->br();
                    } else {
                        $js->appendln("jQuery('#" . $t->element_id . "')." . $t->event . "(function(event) {")->br();
                    }
                    //submit method
                    $js->incIndent()->appendln("jQuery('#" . $this->form_id . "').submit();")->br()->decIndent();

                    //closing
                    $js->appendln("});")->br();
                }
            }
        }
        if ($this->auto_set_focus) {
            $js->appendln("
				$('#" . $this->id . "').find(':input:enabled:visible:first:not(.datepicker)').focus();
			");
        }
        $js->appendln($this->jsChild($js->getIndent()))->br();

        return $js->text();
    }

}

?>