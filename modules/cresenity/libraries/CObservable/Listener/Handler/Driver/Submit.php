<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 7, 2018, 8:32:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_Driver_Submit extends CObservable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver_Submit;

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $formId;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->formId = "";
    }

    public function setTarget($target) {

        $this->target = $target;

        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setForm($form_id) {
        $this->formId = $form_id;
        return $this;
    }

    public function content() {
        return $this->content;
    }

    public function script() {
        $js = '';
        if (strlen($this->formId) == 0) {
            $js .= "
				$('#" . $this->owner . "').closest('form').submit();;
			";
        } else {
            $js .= "
				$('#" . $this->formId . "').submit();;
			";
        }

        return $js;
    }

}
