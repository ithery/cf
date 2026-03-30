<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_Handler_EmptyHandler extends CObservable_Listener_Handler {
    use CObservable_Listener_Handler_Trait_TargetHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Empty';
    }

    public function js() {
        $js = '';

        $js .= "
			jQuery('#" . $this->target . "').empty();

		";

        return $js;
    }
}
