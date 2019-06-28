<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:44:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
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
