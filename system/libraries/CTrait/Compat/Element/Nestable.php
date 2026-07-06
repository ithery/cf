<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_Nestable
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Nestable {
    /**
     * @deprecated since version 1.2
     *
     * @param CTreeDB $treedb
     * @param int     $parent_id
     *
     * @return $this
     */
    public function set_data_from_treedb($treedb, $parent_id = null) {
        /** @var CElement_Component_Nestable $this */
        return $this->setDataFromTreeDb($treedb, $parent_id);
    }

    /**
     * @param bool $boolean
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function set_applyjs($boolean) {
        /** @var CElement_Component_Nestable $this */
        return $this->setApplyJs($boolean);
    }

    /**
     * @param string $style
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function set_action_style($style) {
        /** @var CElement_Component_Nestable $this */
        return $this->setRowActionStyle($style);
    }

    /**
     * @return int
     *
     * @deprecated since 1.2
     */
    public function action_count() {
        /** @var CElement_Component_Nestable $this */
        return $this->rowActionCount();
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     *
     * @deprecated since 1.2
     */
    public function add_row_action($id = '') {
        /** @var CElement_Component_Nestable $this */
        return $this->addRowAction($id);
    }

    /**
     * @return bool
     *
     * @deprecated since 1.2
     */
    public function have_action() {
        /** @var CElement_Component_Nestable $this */
        return $this->haveRowAction();
    }

    /**
     * @param callable|Closure $func
     * @param string           $require
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function display_callback_func($func, $require = '') {
        /** @var CElement_Component_Nestable $this */
        return $this->setDisplayCallback($func, $require);
    }

    /**
     * @param callable|Closure $func
     * @param string           $require
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function filter_action_callback_func($func, $require = '') {
        /** @var CElement_Component_Nestable $this */
        return $this->filterActionCallbackFunc($func, $require);
    }

    /**
     * @deprecated since 1.2
     *
     * @param string $idKey
     *
     * @return $this
     */
    public function set_id_key($idKey) {
        /** @var CElement_Component_Nestable $this */
        return $this->setIdKey($idKey);
    }

    /**
     * @param bool $disableDnd
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function set_disable_dnd($disableDnd) {
        /** @var CElement_Component_Nestable $this */
        return $this->setDisableDnd($disableDnd);
    }

    /**
     * @param bool $checkbox
     *
     * @return $this
     *
     * @deprecated since 1.2
     */
    public function set_have_checkbox($checkbox) {
        /** @var CElement_Component_Nestable $this */
        return $this->setHaveCheckbox($checkbox);
    }

    /**
     * @deprecated since 1.2
     *
     * @param string $valueKey
     *
     * @return $this
     */
    public function set_value_key($valueKey) {
        /** @var CElement_Component_Nestable $this */
        return $this->setValueKey($valueKey);
    }

    /**
     * @deprecated since 1.2
     *
     * @param string $input
     *
     * @return $this
     */
    public function set_input($input) {
        /** @var CElement_Component_Nestable $this */
        return $this->setInput($input);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $a
     *
     * @return $this
     */
    public function set_data_from_array($a) {
        /** @var CElement_Component_Nestable $this */
        return $this->setDataFromArray($a);
    }

    /**
     * @deprecated since version 1.4, use setDisplayCallback
     *
     * @param mixed $func
     * @param mixed $require
     *
     * @return $this
     */
    public function displayCallbackFunc($func, $require = '') {
        /** @var CElement_Component_Nestable $this */
        return $this->setDisplayCallback($func, $require);
    }
}
