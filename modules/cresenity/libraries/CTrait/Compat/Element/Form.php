<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Form {

    /**
     * Set method attribute value of form element
     * 
     * @deprecated since version 1.2, please use setMethod
     * @param string $method POST|GET|PUT|DELETE
     * @return CElement_Component_Form
     */
    public function set_method($method) {
        return $this->setMethod($method);
    }

    /**
     * Set action attribute value of form element
     * 
     * @deprecated since version 1.2, please use setAction
     * @param string $action action attribute of form
     * @return CElement_Component_Form
     */
    public function set_action($action) {
        return $this->setAction($action);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setAjaxSubmit
     * @param bool $bool
     * @return CElement_Component_Form
     */
    public function set_ajax_submit($bool) {
        return $this->setAjaxSubmit($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setAjaxSubmitTarget
     * @param string $target id target element to render of submit response
     * @return CElement_Component_Form
     */
    public function set_ajax_submit_target($target) {
        return $this->SetAjaxSubmitTarget($target);
    }

}
