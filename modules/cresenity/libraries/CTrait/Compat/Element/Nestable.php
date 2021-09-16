<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 2:53:27 PM
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
        return $this->setDataFromTreeDb($treedb, $parent_id);
    }

    public function set_applyjs($boolean) {
        return $this->setApplyJs($boolean);
    }

    public function set_action_style($style) {
        return $this->setRowActionStyle($style);
    }

    public function action_count() {
        return $this->rowActionCount();
    }

    public function add_row_action($id = '') {
        return $this->addRowAction($id);
    }

    public function have_action() {
        return $this->haveRowAction();
    }

    public function display_callback_func($func, $require = '') {
        return $this->displayCallbackFunc($func, $require);
    }

    public function filter_action_callback_func($func, $require = '') {
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
        return $this->setIdKey($idKey);
    }

    public function set_disable_dnd($disableDnd) {
        return $this->setDisableDnd($disableDnd);
    }

    public function set_have_checkbox($checkbox) {
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
        return $this->setInput($input);
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     *
     * @param mixed $a
     */
    public function set_data_from_array($a) {
        return $this->setDataFromArray($a);
    }
}
