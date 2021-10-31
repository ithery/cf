<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 10:51:38 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Textarea {
    /**
     * @param mixed $col
     *
     * @deprecated since version 1.2, use setCol
     */
    public function set_col($col) {
        return $this->setCol($col);
    }

    /**
     * @param mixed $row
     *
     * @deprecated since version 1.2 , use setRow
     */
    public function set_row($row) {
        return $this->setRow($row);
    }

    /**
     * @param mixed $placeholder
     *
     * @deprecated since version 1.2, use setPlaceholder
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }
}
