<?php

class CMobile_Element_Action extends CMobile_CompositeElement {

    protected $jsfunc;
    protected $icon;
    protected $disabled;
    protected $type;
    protected $link_target;
    protected $link;
    protected $label;
    protected $orig_label;
    protected $submit;
    protected $submit_to;
    protected $submit_to_target;
    protected $jsparam;
    protected $confirm;
    protected $style;
    protected $confirm_message;
    protected $button;
    protected $color;
    protected $waves_color;
    protected $fixed;
	protected $large;

    public function __construct($id) {
        parent::__construct($id);

        $this->jsfunc = "";
        $this->type = "jsfunc";
        $this->icon = "";
        $this->link = "";
        $this->jsparam = array();
        $this->link_target = "";
        $this->submit = false;
        $this->submit_to = false;
        $this->submit_to_target = false;
        $this->label = "";
        $this->orig_label = "";
        $this->style = "";
        $this->disabled = false;
        $this->confirm = false;
        $this->confirm_message = "";
        $this->button = false;
		$this->color = "";
        $this->tag = 'a';
        $this->waves_color = 'light';
        $this->fixed = false;
		$this->large = false;
    }

    public static function factory($id = '') {
        return new CMobile_Element_Action($id);
    }

    public function set_icon($ic) {
        $this->icon = $ic;
        return $this;
    }

    public function set_confirm($bool) {
        $this->confirm = $bool;
        return $this;
    }

    public function set_fixed($fixed) {
        $this->fixed = $fixed;
        return $this;
    }

    public function set_large($large) {
        $this->large = $large;
        return $this;
    }
    
    public function set_confirm_message($message) {
        $this->confirm_message = $message;
        return $this;
    }

	public function set_color($color) {
		$this->color = $color;
		return $this;
	}

    public function set_type($type) {
        $this->type = $type;
        return $this;
    }

    public function set_label($label, $lang = true) {
        $this->orig_label = $label;
		if ($lang)
            $label = clang::__($label);
        $this->label = $label;
        return $this;
    }
	
	public function get_label() {
        
		return $this->orig_label;
        
    }
	
    public function set_jsfunc($jsfunc) {
        $this->jsfunc = $jsfunc;
        return $this;
    }

    public function set_jsparam($jsparam) {
        $this->jsparam = $jsparam;
        return $this;
    }

    public function set_link($link) {
        $this->type = "link";
        $this->link = $link;
        return $this;
    }

    public function set_link_target($link_target) {
        $this->link_target = $link_target;
        return $this;
    }

    public function set_submit($bool) {
        $this->submit = $bool;

        return $this;
    }

    public function set_submit_to($url, $target = "") {
        $this->submit_to = $url;

        if (strlen($target) > 0) {
            $this->submit_to_target = $target;
        }
        return $this;
    }
    
    public function set_disabled($bool) {
        $this->disabled = $bool;
        return $this;
    }
    
    public function set_button($bool) {
        $this->button = $bool;
        return $this;
    }

    public function set_waves_color($waves_color) {
        $this->waves_color = $waves_color;
        return $this;
    }

    public function render_as_input() {
        $render_as_input = false;
        // if ($this->submit) {
        //     $render_as_input = true;
        // }
        return $render_as_input;
    }

    public function reassign_confirm() {
        if ($this->confirm) {
            //we check the listener
            if (count($this->listeners) > 0) {
                foreach ($this->listeners as $lis) {
                    $lis->set_confirm(true)->set_confirm_message($this->confirm_message);
                }
                $this->set_confirm(false);
            }
        }
    }

    
	public function build() {
        if($this->submit) {
            $this->tag = 'input';
            $this->add_attr('type', 'submit');
            $this->add_attr('value', $this->label);
        } else {
            $this->add($this->label);
        }
		$this->reassign_confirm();
		if ($this->disabled) {
			$this->set_attr('disabled','disabled');
		}
		if (strlen($this->link_target)) {
			$this->set_attr('target',$this->link_target);
		}
		
		if ($this->confirm) {
			$this->add_class('confirm');
		}
		
		if (strlen($this->confirm_message)>0) {
			$this->set_attr('data-confirm-message',base64_encode($this->confirm_message));
		}
		
		$jsparam = $this->jsparam;
		$link = $this->link;
			
		//replace href attr if have param
		$param = "";
		$i = 0;
		foreach ($jsparam as $k => $p) {
			$i++;
			if ($k == "param1") {
				if (strlen($param) > 0)
					$param .= ",";
				$param .= "'" . $p . "'";
			}
			if ($this->type == "link") {
				//$link = str_replace("{param1}",$p,$link);
				preg_match_all("/{([\w]*)}/", $link, $matches, PREG_SET_ORDER);
				foreach ($matches as $val) {
					$str = $val[1]; //matches str without bracket {}
					$b_str = $val[0]; //matches str with bracket {}
					if ($k == $str) {
						$link = str_replace($b_str, $p, $link);
					}
				}
			}
		}
		
		if($link=="") {
			$link = "javascript:;";
		}
		
		$this->set_attr('href',$link);
		
		$waves_color = '';
        if(strlen($this->waves_color) > 0) {
            $waves_color = ' waves-' . $this->waves_color;
        }
        if(strlen($this->color) > 0) {
            $this->add_class($this->color);
        }
        if($this->fixed) {
            $this->add_class('fixed-action-btn');
        }
        if($this->large) {
            $this->add_class('btn-large');
        }
        $this->add_class('btn waves-effect waves-light');
		$this->add_class($waves_color);
		
		if (strlen($this->icon) > 0) {
			$this->add_icon()->set_icon($this->icon);
		}
		// $this->add($this->label);
	}
	
	

    public function js($indent = 0) {
        $this->reassign_confirm();
        $js = new CStringBuilder();
        $js->set_indent($indent);

        if ($this->disabled) {
            $js->appendln("jQuery('#" . $this->id . "').click(function(e) { e.preventDefault(); });");
        } else {
            if ($this->render_as_input()) {

                if (strlen($this->link) > 0) {
                    if ($this->submit) {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { jQuery(this).closest('form').attr('action','" . $this->link . "'); });");
                    } else {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { window.location.href='" . $this->link . "'; });");
                    }
                } else {
                    // $js->appendln("jQuery('#" . $this->id . "').click(function() { jQuery(this).closest('form').attr('action','" . $this->link . "'); });");
                }
            } else {
                //     print_r('xxxxx' . $this->submit_to);
                // die();
                // if (strlen($this->submit_to) > 0) {
     //                $str_submit_to_target = "";
     //                if (strlen($this->submit_to_target) > 0) {
     //                    $str_submit_to_target = "jQuery(this).closest('form').attr('target','" . $this->submit_to_target . "');";
     //                }
     //                $js->appendln("
					// 	jQuery('#" . $this->id . "').click(function() {
					// 		jQuery(this).closest('form').attr('action','" . $this->submit_to . "');
					// 		" . $str_submit_to_target . "
					// 		jQuery(this).closest('form').submit();
					// 	}
					// );");
                // }
            }
        }



        $js->appendln(parent::js($js->get_indent()))->br();
        return $js->text();
    }

}

?>