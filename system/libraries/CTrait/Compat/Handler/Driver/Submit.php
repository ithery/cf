<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:37:03 PM
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
