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
    protected $ajax_submit;
    protected $ajax_success_script_callback;
    protected $ajax_datatype;
    protected $ajax_redirect;
    protected $ajax_redirect_url;
    protected $ajax_submit_handlers;
    protected $ajax_submit_target;
    protected $ajax_submit_target_class;
    protected $auto_set_focus;
    protected $action_before_submit;
    protected $disable_js;
    protected $submitListener;
    protected $validationPromptPosition;

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
        $this->validation = false;
        $this->ajax_submit = false;
        $this->ajax_success_script_callback = "";
        $this->ajax_datatype = "text";
        $this->ajax_redirect = true;
        $this->ajax_redirect_url = "";
        $this->ajax_submit_handlers = array();
        $this->ajax_submit_target = false;
        $this->ajax_submit_target_class = false;
        $this->auto_set_focus = true;
        $this->action_before_submit = '';
        $this->disable_js = false;
        $this->validationPromptPosition = "topRight";

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

    public function setValidationPromptPosition($position) {
        $this->validationPromptPosition = $position;
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
            $this->validation = false;
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

    public function setAjaxDataType($datatype) {
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

    public function set_ajax_redirect_url($url) {
        $this->ajax_redirect_url = $url;
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
        if (strlen($this->remoteValidationUrl) > 0) {
            $this->setAttr('remote-validation-url', $this->remoteValidationUrl);
        }
        if (strlen($this->layout) > 0) {
            $this->addClass('form-' . $this->layout);
        }

        if ($this->validation) {
            CManager::registerModule('validation');
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

            $js->appendln("
				var cprocess_run_once_" . $this->id . " = false;
				var ctimer_" . $this->id . " = false;
			");



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


						var form_ajax_url = $('#" . $this->id . "').attr('action');
						if(!form_ajax_url) form_ajax_url = '" . $ajax_url . "';
						var options = {
							url: form_ajax_url,
							dataType: '" . $this->ajax_datatype . "',
							type: '" . $this->method . "',
							success: function(data) {
                                                            $.cresenity._handle_response(data,function() {
                                                                    $('#" . $this->id . "').find('*').removeClass('disabled');
                                                                    $('#" . $this->id . "').removeClass('loading');
                                                                    " . $on_success_script . "
                                                                });
                                                                //do callback


							},

							error: function(xhr, status, error) {
								$('#" . $this->id . "').find('*').removeClass('disabled');
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
                $strvalidation = "$('#" . $this->id . "').validationEngine('attach', {promptPosition:'" . $this->validationPromptPosition . "'});";
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