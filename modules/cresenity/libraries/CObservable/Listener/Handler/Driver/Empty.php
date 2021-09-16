<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 4:28:29 PM
 */
class CObservable_Listener_Handler_Driver_Empty extends CObservable_Listener_Handler_Driver {
    use CTrait_Compat_Handler_Driver_Empty;

    protected $method;

    protected $content;

    protected $param;

    protected $param_inputs;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = 'get';
        $this->target = '';
        $this->content = CHandlerElement::factory();
        $this->param_inputs = [];
    }

    public function addParamInput($inputs) {
        if (!is_array($inputs)) {
            $inputs = [$inputs];
        }
        foreach ($inputs as $inp) {
            $this->param_inputs[] = $inp;
        }
        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function content() {
        return $this->content;
    }

    public function script() {
        $js = '';
        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0) {
                $data_addition .= ',';
            }
            $data_addition .= "'" . $inp . "':$('#" . $inp . "').val()";
        }
        $data_addition = '{' . $data_addition . '}';
        $js .= "
			jQuery('#" . $this->target . "').empty();

		";

        return $js;
    }
}
