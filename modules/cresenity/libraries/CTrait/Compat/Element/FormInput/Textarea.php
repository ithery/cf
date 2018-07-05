<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 10:51:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Textarea {

    /**
     * @deprecated since version 1.2
     */
    public function set_col($col) {
        return $this->setCol($col);
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_row($row) {
        return $this->setCol($row);
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_placeholder($placeholder) {
        return $this->setPlaceholder($placeholder);
    }

}
