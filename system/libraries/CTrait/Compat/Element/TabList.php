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
     * @return CElement_Component_TabList_Tab
     *
     * @deprecated since 1.2, use addTab
     */
    public function add_tab($id = '') {
        return $this->addTab($id);
    }

    public function active_tab($tabId) {
        return $this->setActiveTab($tabId);
    }

    public function set_active_tab($tabId) {
        return $this->setActiveTab($tabId);
    }

    public function add_widget_class($class) {
        return $this->addWidgetClass($class);
    }

    public function set_tab_position($tabPosition) {
        return $this->setTabPosition($tabPosition);
    }

    public function set_ajax($bool = true) {
        return $this->setAjax($bool);
    }
}
