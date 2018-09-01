<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:16:08 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Listener_Handler_Driver_Remove extends CRenderable_Listener_Handler_Driver {

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $param_inputs;
    protected $parent;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = $owner;
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
    }

    public function script() {
        $js = 'jQuery("#' . $this->target . '").remove()';
        return $js;
    }

}
