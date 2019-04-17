<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 17, 2019, 11:17:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_Driver_AjaxSubmit extends CObservable_Listener_Handler_Driver {

    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    public function script() {
        $optionsArray = array();
        $optionsJson = json_encode($optionsArray);
        $js = '';
        $js .= "
            $.cresenity.ajaxSubmit('#" . $this->owner . "','" . $this->generatedUrl() . "'," . $optionsJson . ");;
         ";
        return $js;
    }

}
