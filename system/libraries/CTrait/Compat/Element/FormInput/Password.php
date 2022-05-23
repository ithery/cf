<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 2:37:44 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Password {
    /**
     * @deprecated, please use setPlaceholder
     *
     * @param string $placeholder
     *
     * @return $this
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

    /**
     * @deprecated, please use setAutoComplete
     *
     * @param string $bool
     *
     * @return $this
     */
    public function set_autocomplete($bool) {
        return $this->setAutocomplete($bool);
    }
}
