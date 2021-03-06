<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:58:12 PM
 */
class CObservable_Listener_Handler_AjaxSubmitHandler extends CObservable_Listener_Handler {
    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->name = 'AjaxSubmit';
    }

    public function js() {
        $optionsJson = '{';
        $optionsJson .= "selector:'#" . $this->owner . "',";
        if ($this->haveCompleteListener()) {
            $optionsJson .= 'onComplete: ' . $this->getCompleteListener()->js() . ',';
        }
        if ($this->haveSuccessListener()) {
            $optionsJson .= 'onSuccess: ' . $this->getSuccessListener()->js() . ',';
        }
        $optionsJson .= 'handleJsonResponse: true,';
        $optionsJson .= '}';
        $js = '';
        $js .= '
            cresenity.ajaxSubmit(' . $optionsJson . ');;
         ';
        return $js;
    }
}
