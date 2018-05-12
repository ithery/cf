<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 2:16:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_StringBuilder {

    /**
     * 
     * @deprecated since version 1.2
     * @param type $ind
     * @return type
     */
    public function set_indent($ind) {
        return $this->setIndent($ind);
    }

    /**
     * @deprecated since version 1.2
     */
    public function get_indent() {
        return $this->getIndent();
    }

    /**
     * @deprecated since version 1.2
     */
    public function inc_indent($n = 1) {
        return $this->incIndent($n);
    }

    /**
     * @deprecated since version 1.2
     */
    public function dec_indent($n = 1) {
        return $this->decIndent($n);
    }

}
