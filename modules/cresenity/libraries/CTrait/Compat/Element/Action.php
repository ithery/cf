<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:01:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Action {

    /**
     * @deprecated since version 1.2
     */
    public function get_label() {
        return $this->getLabel();
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_icon($ic) {
        return $this->setIcon($ic);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setSubmit
     * @param CElement_Component_Action
     */
    public function set_submit($bool) {
        return $this->setSubmit($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setLink
     * @param CElement_Component_Action
     */
    public function set_link($link) {
        return $this->setLink($link);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setConfirm
     * @param CElement_Component_Action
     */
    public function set_confirm($bool) {
        return $this->setConfirm($bool);
    }

}
