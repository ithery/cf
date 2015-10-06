<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Tooltip_Driver extends CHandler_Driver {

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $title;
    protected $actions;
    protected $param_inputs;
    protected $reload_page;
    protected $callback;
    protected $js_class;
    protected $text;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->actions = CActionList::factory();
        $this->param_inputs = array();
        $this->text = '';
    }
   
    
    public function set_reload_page($reload_page){
        $this->reload_page = $reload_page;
        return $this;
    }
    
    public function set_callback(callable $callback){
        $this->callback = $callback;
        return $this;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function set_target($target) {
        $this->target = $target;
        return $this;
    }
    public function set_text($text) {
        $this->text = $text;
        return $this;
    }
    
    public function set_js_class($js_class) {
        $this->js_class = $js_class;
        return $this;
    }

    public function add_param_input($inputs) {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        foreach ($inputs as $inp) {
            $this->param_inputs[] = $inp;
        }
        return $this;
    }

    public function set_method($method) {
        $this->method = $method;
    }

    public function content() {
        return $this->content;
    }

    public function script() {
        $js = parent::script();
        if (strlen($this->target) == 0) {
            $this->target = "modal_opt_" . $this->event . "_" . $this->owner . "_dialog";
        }

        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0) $data_addition.=',';
            $data_addition.="'" . $inp . "':$.cresenity.value('#" . $inp . "')";
        }
        $data_addition = '{' . $data_addition . '}';
        /*
          $js.= "
          var modal_opt_".$this->event."_".$this->owner." = {
          id: 'modal_opt_".$this->event."_".$this->owner."_dialog', // id which (if specified) will be added to the dialog to make it accessible later
          autoOpen: true , // Should the dialog be automatically opened?
          title: '".$this->title."',
          content: '".$this->generated_url()."',
          buttons: {

          },
          closeOnOverlayClick: true , // Should the dialog be closed on overlay click?
          closeOnEscape: true , // Should the dialog be closed if [ESCAPE] key is pressed?
          removeOnClose: true , // Should the dialog be removed from the document when it is closed?
          showCloseHandle: true , // Should a close handle be shown?
          initialLoadText: '' // Text to be displayed when the dialogs contents are loaded
          }
          jQuery('<div/>').dialog2(modal_opt_".$this->event."_".$this->owner.");
          ";
         */
        $js_class = ccfg::get('js_class');
        if (strlen($js_class) > 0) {
            $this->js_class = $js_class;
        }
        if (strlen($this->js_class) > 0) {
            if ($this->content instanceof CHandlerElement) {
                $content = $this->content->html();
            }
            else {
                $content = $this->content;
            }
            $content = addslashes($content);
            $content = str_replace("\r\n", "", $content);
            $js .= "
                $." .$this->js_class .".show_tooltip('" .$this->owner ."','" .$this->text ."','" .$content ."');
                ";
        }
        else {
            if(strlen($this->url) == 0) {
                $js.= "
                    $.cresenity.show_tooltip('" .$this->owner ."', '', '','" .$this->text ."');
                ";
            } else {
                $js.= "
                    $.cresenity.show_tooltip('" . $this->owner . "','" . $this->generated_url() . "','" . $this->method . "','" . $this->text . "'," . $data_addition . ");
                ";
            }
        }
        return $js;
    }

}
    