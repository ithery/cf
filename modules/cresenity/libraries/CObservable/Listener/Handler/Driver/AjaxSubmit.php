<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 17, 2019, 11:17:21 PM
 */
class CObservable_Listener_Handler_Driver_AjaxSubmit extends CObservable_Listener_Handler_Driver {
    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    public function script() {
        $optionsArray = [];
        $optionsJson = json_encode($optionsArray);
        $js = '';
        $js .= "
            cresenity.ajaxSubmit('#" . $this->owner . "'," . $optionsJson . ');;
         ';
        return $js;
    }
}
