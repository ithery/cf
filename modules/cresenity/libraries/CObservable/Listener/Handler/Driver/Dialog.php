<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:06:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_Driver_Dialog extends CObservable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver_Dialog,
        CTrait_Element_Property_Title,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $actions;
    protected $param_inputs;
    protected $param_request;
    protected $js_class;
    protected $js_class_manual;
    protected $isSidebar;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->actions = CActionList::factory();
        $this->param_inputs = array();
        $this->param_request = array();
        $this->title = 'Detail';
        $this->js_class = null;
        $this->js_class_manual = null;
    }

    public function setSidebar($bool = true) {
        $this->isSidebar = $bool;
        return $this;
    }

    /**
     * @deprecated since version 1.2
     * @param type $js_class
     * @return $this
     */
    public function set_js_class($js_class) {
        //set js class manual
        $this->js_class_manual = $js_class;
        return $this;
    }

    public function addParamInput($inputs) {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        foreach ($inputs as $inp) {
            $this->param_inputs[] = $inp;
        }
        return $this;
    }

    public function add_param_request($param_request) {
        if (!is_array($param_request)) {
            $param_request = array($param_request);
        }
        foreach ($param_request as $req_k => $req_v) {
            $this->param_request[$req_k] = $req_v;
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
        $js = '';
        if (strlen($this->target) == 0) {
            $this->target = "modal_opt_" . $this->event . "_" . $this->owner . "_dialog";
        }

        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0) {
                $data_addition .= ',';
            }
            $data_addition .= "'" . $inp . "':$.cresenity.value('#" . $inp . "')";
        }
        foreach ($this->param_request as $req_k => $req_v) {
            if (strlen($data_addition) > 0) {
                $data_addition .= ',';
            }
            $data_addition .= "'" . $req_k . "':'" . $req_v . "'";
        }
        $data_addition = '{' . $data_addition . '}';

        $optionsArray = array();
        $optionsArray['title'] = $this->title;
        $optionsArray['isSidebar'] = $this->isSidebar;
        $optionsJson = json_encode($optionsArray);
        $js_class = ccfg::get('js_class');
        if (strlen($js_class) > 0) {
            $this->js_class = $js_class;
        }
        if ($this->js_class_manual != null) {
            $this->js_class = $this->js_class_manual;
        }
        if (strlen($this->js_class) > 0 && $this->js_class != 'cresenity') {
            if ($this->content instanceof CHandlerElement) {
                $content = $this->content->html();
            } else {
                $content = $this->content;
            }
            $content = addslashes($content);
            $content = str_replace("\r\n", "", $content);
            if (strlen(trim($content)) == 0) {
                $content = $this->generatedUrl();
            }
            $js .= "
                $." . $this->js_class . ".show_dialog('" . $this->target . "','" . $this->title . "','" . $content . "');
            ";
        } else {
            $js .= "
                $.cresenity.show_dialog('" . $this->target . "','" . $this->generatedUrl() . "','" . $this->method . "'," . $optionsJson . "," . $data_addition . ");
            ";
        }
        return $js;
    }

}
