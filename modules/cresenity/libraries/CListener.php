<?php

class CListener extends CObject {
    
    use CTrait_Compat_Listener;
    
    protected $event;
    protected $handlers;
    protected $owner;
    protected $confirm;
    protected $confirm_message;
    protected $no_double;

    protected function __construct($owner, $event) {
        parent::__construct();


        $this->owner = $owner;
        $this->handlers = array();
        $this->confirm = false;
        $this->confirm_message = "";
        $this->no_double = false;
        $this->event = $event;
    }

    public static function factory($owner, $event) {

        return new CListener($owner, $event);
    }

    public function setConfirm($bool) {
        $this->confirm = $bool;
        return $this;
    }

    public function setNoDouble($bool) {
        $this->no_double = $bool;
        return $this;
    }

    public function setConfirmMessage($message) {
        $this->confirm_message = $message;
        return $this;
    }

    public function owner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
        //we set all handler owner too
        foreach ($this->handlers as $handler) {
            $handler->set_owner($owner);
        }
        return $this;
    }

    public function handlers() {
        return $this->handlers;
    }

    public function setHandlerUrlParam($param) {

        foreach ($this->handlers as $handler) {
            $handler->set_url_param($param);
        }
    }

    /**
     * 
     * @param string $handler_name
     * @return CHandler
     */
    public function addHandler($handler_name) {
        $handler = CHandler::factory($this->owner, $this->event, $handler_name);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);


        $start_script = "
				var thiselm=jQuery(this);
				var clicked = thiselm.attr('data-clicked');
			";
        if ($this->no_double) {
            $start_script.="
					if(clicked) return false;
				";
        }
        $start_script.="
				thiselm.attr('data-clicked','1');
			";
        $handlers_script = "";
        foreach ($this->handlers as $handler) {
            $handlers_script.= $handler->js();
        }
        $confirm_start_script = "";
        $confirm_end_script = "";
        if ($this->confirm) {

            $confirm_message = $this->confirm_message;
            if (strlen($confirm_message) == 0) {
                $confirm_message = clang::__('Are you sure') . " ?";
            }
            $confirm_start_script = "
				
				
				
				bootbox.confirm('" . $confirm_message . "', function(confirmed) {
					if(confirmed) {
			";

            $confirm_end_script = "
					} else {
						thiselm.removeAttr('data-clicked');
					}
				});
			";
        }

        if ($this->event == 'lazyload') {
            $js->append("
                    jQuery(window).ready(function() {
                        if (jQuery('#" . $this->owner . "')[0].getBoundingClientRect().top < (jQuery(window).scrollTop() + jQuery(window).height())) {
                                " . $start_script . "
                                " . $confirm_start_script . "
                                " . $handlers_script . "
                                " . $confirm_end_script . "
                            }
                    });
                    jQuery(window).scroll(function() {
                        if (jQuery('#" . $this->owner . "')[0].getBoundingClientRect().top < (jQuery(window).scrollTop() + jQuery(window).height())) {
                                " . $start_script . "
                                " . $confirm_start_script . "
                                " . $handlers_script . "
                                " . $confirm_end_script . "
                            }
                    });
                ");
        } else {
            $js->append('
                    jQuery("#' . $this->owner . '").' . $this->event . '(function() {

                        ' . $start_script . '
                        ' . $confirm_start_script . '
                        ' . $handlers_script . '
                        ' . $confirm_end_script . '

                    });
                ');
        }

        //           $js->append("
        // 	jQuery('#" . $this->owner . "')." . $this->event . "(function() {				
        // 		" . $start_script . "
        // 		" . $confirm_start_script . "
        // 		" . $handlers_script . "
        // 		" . $confirm_end_script . "
        // 	});
        // ");

        return $js->text();
    }

}
