<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_TabList
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_TabList {
    /**
     * @param string $id
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated since 1.2, use addTab
     */
    public function add_tab($id = '') {
        /** @var CElement_List_TabList $this */
        return $this->addTab($id);
    }

    /**
     * @param string $tabId
     *
     * @return $this
     *
     * @deprecated since 1.2, use setActiveTab
     */
    public function active_tab($tabId) {
        /** @var CElement_List_TabList $this */
        return $this->setActiveTab($tabId);
    }

    /**
     * @param string $tabId
     *
     * @return $this
     *
     * @deprecated since 1.2, use setActiveTab
     */
    public function set_active_tab($tabId) {
        /** @var CElement_List_TabList $this */
        return $this->setActiveTab($tabId);
    }

    /**
     * @param string|array $class
     *
     * @return $this
     *
     * @deprecated since 1.2, use addWidgetClass
     */
    public function add_widget_class($class) {
        /** @var CElement_List_TabList $this */
        return $this->addWidgetClass($class);
    }

    /**
     * @param string $tabPosition
     *
     * @return $this
     *
     * @deprecated since 1.2, use setTabPosition
     */
    public function set_tab_position($tabPosition) {
        /** @var CElement_List_TabList $this */
        return $this->setTabPosition($tabPosition);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since 1.2, use setAjax
     */
    public function set_ajax($bool = true) {
        /** @var CElement_List_TabList $this */
        return $this->setAjax($bool);
    }
}
