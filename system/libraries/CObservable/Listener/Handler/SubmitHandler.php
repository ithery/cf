<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:32:34 PM
 */
class CObservable_Listener_Handler_SubmitHandler extends CObservable_Listener_Handler {
    use CTrait_Compat_Handler_Driver_Submit,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    protected $formId;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Submit';
        $this->formId = '';
    }

    public function setForm($formId) {
        $this->formId = $formId;

        return $this;
    }

    public function js() {
        $js = '';
        if (strlen($this->formId) == 0) {
            $js .= "$('#" . $this->owner . "').closest('form').submit();";
        } else {
            $js .= "$('#" . $this->formId . "').submit();";
        }

        return $js;
    }
}
