<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 3:43:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener {

    use CTrait_Compat_Listener;

    protected $event;
    protected $handlers;
    protected $owner;
    protected $confirm;
    protected $confirm_message;
    protected $no_double;

    public function __construct($owner, $event) {
        $this->owner = $owner;
        $this->handlers = array();
        $this->confirm = false;
        $this->confirm_message = "";
        $this->no_double = false;
        $this->event = $event;
    }

    public static function factory($owner, $event) {

        return new CObservable_Listener($owner, $event);
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
     * @param string $handlerName
     * @return CObservable_Listener_Handler
     */
    public function addHandler($handlerName) {
        $handler = new CObservable_Listener_Handler($this->owner, $this->event, $handlerName);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);


        $startScript = "
            var thiselm=jQuery(this);
            var clicked = thiselm.attr('data-clicked');
        ";
        if ($this->no_double) {
            $startScript .= "
                if(clicked) return false;
            ";
        }
        $startScript .= "
            thiselm.attr('data-clicked','1');
        ";
        $handlersScript = "";
        foreach ($this->handlers as $handler) {
            $handlersScript .= $handler->js();
        }
        $confirmStartScript = "";
        $confirmEndScript = "";
        if ($this->confirm) {

            $confirm_message = $this->confirm_message;
            if (strlen($confirm_message) == 0) {
                $confirm_message = clang::__("Are you sure ?");
            }
            $confirmStartScript = "	
                bootbox.confirm('" . $confirm_message . "', function(confirmed) {
                    if(confirmed) {
            ";

            $confirmEndScript = "
                    } else {
                        thiselm.removeAttr('data-clicked');
                    }
                    var modalExists = $('.modal:visible').length > 0;
                    if (!modalExists) {
                        if($('body').hasClass('modal-open')) {
                            $('body').removeClass('modal-open');
                        }
                    } else {
                        if(!$('body').hasClass('modal-open')) {
                            $('body').addClass('modal-open');
                        }
                    }
                });
            ";
        }

        if ($this->event == 'lazyload') {
            $js->append("
                    jQuery(window).ready(function() {
                        if (jQuery('#" . $this->owner . "')[0].getBoundingClientRect().top < (jQuery(window).scrollTop() + jQuery(window).height())) {
                                " . $startScript . "
                                " . $confirmStartScript . "
                                " . $handlersScript . "
                                " . $confirmEndScript . "
                            }
                    });
                    jQuery(window).scroll(function() {
                        if (jQuery('#" . $this->owner . "')[0].getBoundingClientRect().top < (jQuery(window).scrollTop() + jQuery(window).height())) {
                                " . $startScript . "
                                " . $confirmStartScript . "
                                " . $handlersScript . "
                                " . $confirmEndScript . "
                            }
                    });
                ");
        } else {
            $js->append('
                    jQuery("#' . $this->owner . '").' . $this->event . '(function() {

                        ' . $startScript . '
                        ' . $confirmStartScript . '
                        ' . $handlersScript . '
                        ' . $confirmEndScript . '

                    });
                ');
        }

        //           $js->append("
        // 	jQuery('#" . $this->owner . "')." . $this->event . "(function() {				
        // 		" . $startScript . "
        // 		" . $confirmStartScript . "
        // 		" . $handlersScript . "
        // 		" . $confirmEndScript . "
        // 	});
        // ");

        return $js->text();
    }

}
