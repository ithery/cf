<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:55:05 AM
 */

 //@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Form {
    /**
     * Set method attribute value of form element
     *
     * @deprecated since version 1.2, please use setMethod
     *
     * @param string $method POST|GET|PUT|DELETE
     *
     * @return CElement_Component_Form
     */
    public function set_method($method) {
        return $this->setMethod($method);
    }

    /**
     * Set action attribute value of form element
     *
     * @deprecated since version 1.2, please use setAction
     *
     * @param string $action action attribute of form
     *
     * @return CElement_Component_Form
     */
    public function set_action($action) {
        return $this->setAction($action);
    }

    /**
     * Set target attribute value of form element
     *
     * @deprecated since version 1.2, please use setTarget
     *
     * @param string $target target attribute of form
     *
     * @return CElement_Component_Form
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }

    /**
     * @deprecated since version 1.2, please use setAjaxSubmit
     *
     * @param bool $bool
     *
     * @return CElement_Component_Form
     */
    public function set_ajax_submit($bool) {
        return $this->setAjaxSubmit($bool);
    }

    /**
     * @deprecated since version 1.2, please use setAjaxSubmitTarget
     *
     * @param string $target id target element to render of submit response
     *
     * @return CElement_Component_Form
     */
    public function set_ajax_submit_target($target) {
        return $this->SetAjaxSubmitTarget($target);
    }

    /**
     * @deprecated since version 1.2, please use setAjaxRedirect
     *
     * @param bool $bool
     *
     * @return CElement_Component_Form
     */
    public function set_ajax_redirect($bool) {
        return $this->setAjaxRedirect($bool);
    }

    /**
     * @deprecated since version 1.2, please use setEncType
     *
     * @param string $enctype multipart/form-data
     *
     * @return CElement_Component_Form
     */
    public function set_enctype($enctype = 'multipart/form-data') {
        return $this->setEncType($enctype);
    }

    /**
     * @deprecated since version 1.2, please use setValidation
     *
     * @param bool $bool
     *
     * @return CElement_Component_Form
     */
    public function set_validation($bool) {
        return $this->setValidation($bool);
    }

    /**
     * @deprecated since version 1.2, please use setAutoComplete
     *
     * @param bool $bool
     *
     * @return CElement_Component_Form
     */
    public function set_autocomplete($bool) {
        return $this->setAutoComplete($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $name
     *
     * @return $this
     */
    public function set_name($name) {
        return $this->setName($name);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $layout
     *
     * @return $this
     */
    public function set_layout($layout) {
        return $this->setLayout($layout);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param type $datatype
     *
     * @return $this
     */
    public function set_ajax_datatype($datatype) {
        return $this->setAjaxDataType($datatype);
    }

    public function set_disable_js($bool) {
        $this->disable_js = $bool;
        return $this;
    }

    public function set_ajax_submit_target_class($target) {
        $this->ajax_submit_target_class = $target;
        return $this;
    }

    public function set_ajax_success_script_callback($jsfunc) {
        $this->ajax_success_script_callback = $jsfunc;
        return $this;
    }

    public function setAjaxRedirect($bool) {
        $this->ajax_redirect = $bool;
        return $this;
    }

    public function set_ajax_redirect_url($url) {
        $this->ajax_redirect_url = $url;
        return $this;
    }

    public function set_action_before_submit($action_before_submit) {
        $this->action_before_submit = $action_before_submit;
        return $this;
    }

    public function set_auto_set_focus($bol) {
        $this->auto_set_focus = $bol;
        return $this;
    }

    /**
     * @param string $handler_name
     *
     * @return CHandler
     */
    public function add_ajax_submit_handler($handler_name) {
        $handler = CHandler::factory($this->id, 'submit', $handler_name);
        $this->ajax_submit_handlers[] = $handler;
        return $handler;
    }
}
//@codingStandardsIgnoreEnd
