<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CObservable_Listener_Handler_SubmitHandler
 */
// @codingStandardsIgnoreStart

trait CTrait_Compat_Handler_Driver_Submit {
    /**
     * @param string $formId
     *
     * @return $this
     * @deprecated, please use setForm
     */
    public function set_form($formId) {
        return $this->setForm($formId);
    }

    /**
     * @param string $target
     *
     * @return $this
     * @deprecated, please use setTarget
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }

    /**
     * @param string $method
     *
     * @return $this
     * @deprecated, please use setMethod
     */
    public function set_method($method) {
        return $this->setMethod($method);
    }
}
