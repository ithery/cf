<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_TabList {

    /**
     * 
     * @param string $id
     * @return CElement_Component_TabList_Tab
     */
    public function add_tab($id = "") {
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
