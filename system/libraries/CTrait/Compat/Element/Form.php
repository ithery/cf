<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_Form
 */

 //@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Form {
    /**
     * Set method attribute value of form element.
     *
     * @deprecated since version 1.2, please use setMethod
     *
     * @param string $method POST|GET|PUT|DELETE
     *
     * @return CElement_Component_Form
     */
    public function set_method($method) {
        /** @var CElement_Component_Form $this */
        return $this->setMethod($method);
    }

    /**
     * Set action attribute value of form element.
     *
     * @deprecated since version 1.2, please use setAction
     *
     * @param string $action action attribute of form
     *
     * @return CElement_Component_Form
     */
    public function set_action($action) {
        /** @var CElement_Component_Form $this */
        return $this->setAction($action);
    }

    /**
     * Set target attribute value of form element.
     *
     * @deprecated since version 1.2, please use setTarget
     *
     * @param string $target target attribute of form
     *
     * @return CElement_Component_Form
     */
    public function set_target($target) {
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
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
        /** @var CElement_Component_Form $this */
        return $this->setLayout($layout);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $datatype
     *
     * @return $this
     */
    public function set_ajax_datatype($datatype) {
        /** @var CElement_Component_Form $this */
        return $this->setAjaxDataType($datatype);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function set_disable_js($bool) {
        /** @var CElement_Component_Form $this */
        $this->disable_js = $bool;

        return $this;
    }

    /**
     * @param string $target
     *
     * @return $this
     */
    public function set_ajax_submit_target_class($target) {
        /** @var CElement_Component_Form $this */
        $this->ajax_submit_target_class = $target;

        return $this;
    }

    /**
     * @param string $jsfunc
     *
     * @return $this
     */
    public function set_ajax_success_script_callback($jsfunc) {
        /** @var CElement_Component_Form $this */
        $this->ajax_success_script_callback = $jsfunc;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAjaxRedirect($bool) {
        /** @var CElement_Component_Form $this */
        $this->ajax_redirect = $bool;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function set_ajax_redirect_url($url) {
        /** @var CElement_Component_Form $this */
        $this->ajax_redirect_url = $url;

        return $this;
    }

    /**
     * @param string $action_before_submit
     *
     * @return $this
     */
    public function set_action_before_submit($action_before_submit) {
        /** @var CElement_Component_Form $this */
        $this->action_before_submit = $action_before_submit;

        return $this;
    }

    /**
     * @param bool $bol
     *
     * @return $this
     */
    public function set_auto_set_focus($bol) {
        /** @var CElement_Component_Form $this */
        $this->autoFocus = $bol;

        return $this;
    }

    /**
     * @param string $handler_name
     *
     * @return CHandler
     */
    public function add_ajax_submit_handler($handler_name) {
        /** @var CElement_Component_Form $this */
        $handler = CHandler::factory($this->id, 'submit', $handler_name);
        $this->ajax_submit_handlers[] = $handler;

        return $handler;
    }
}
//@codingStandardsIgnoreEnd
