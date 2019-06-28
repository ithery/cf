<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:44:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_RemoveHandler extends CObservable_Listener_Handler {

    use CTrait_Compat_Handler_Driver_Remove,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait;

    protected $parent;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->target = $this->owner;
        $this->name = 'Remove';
        $this->parent = '';
    }

    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }

    public function js() {
        $js = '';
        $js .= 'jQuery("#' . $this->target . '")';
        if (strlen($this->parent) > 0) {
            $js .= '.parents("' . $this->parent . '")';
        }
        $js .= '.remove();';

        return $js;
    }

}
