<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:28:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Listener_Handler_Driver_Empty extends CRenderable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver_Empty;

    protected $method;
    protected $content;
    protected $param;
    protected $param_inputs;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
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
        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0)
                $data_addition .= ',';
            $data_addition .= "'" . $inp . "':$('#" . $inp . "').val()";
        }
        $data_addition = '{' . $data_addition . '}';
        $js .= "
			jQuery('#" . $this->target . "').empty();
                        
		";

        return $js;
    }

}
