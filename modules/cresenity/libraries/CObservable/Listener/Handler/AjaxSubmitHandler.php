<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:58:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_AjaxSubmitHandler extends CObservable_Listener_Handler {

    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->name = "AjaxSubmit";
    }

    public function js() {
        $optionsArray = array();
        $optionsJson = json_encode($optionsArray);
        $js = '';
        $js .= "
            $.cresenity.ajaxSubmit('#" . $this->owner . "'," . $optionsJson . ");;
         ";
        return $js;
    }

}
