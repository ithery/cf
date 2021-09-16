<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 4:16:08 PM
 */
class CObservable_Listener_Handler_Driver_Remove extends CObservable_Listener_Handler_Driver {
    use CTrait_Compat_Handler_Driver_Remove;

    protected $target;

    protected $method;

    protected $content;

    protected $param;

    protected $param_inputs;

    protected $parent;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = 'get';
        $this->target = $owner;
        $this->content = CHandlerElement::factory();
        $this->param_inputs = [];
    }

    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }

    public function script() {
        $js = '';
        $js .= 'jQuery("#' . $this->target . '")';
        if (strlen($this->parent) > 0) {
            $js .= '.parents("' . $this->parent . '")';
        }
        $js .= '.remove();';

        return $js;
    }
}
