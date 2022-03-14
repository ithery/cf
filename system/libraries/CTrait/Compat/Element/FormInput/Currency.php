<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 6:08:17 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Currency {
    /**
     * @deprecated since version 1.2
     *
     * @param string $placeholder
     *
     * @return string
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }
}
