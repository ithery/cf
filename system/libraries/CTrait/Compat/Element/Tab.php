<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_List_TabList_Tab
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Tab {
    /**
     * @param string $label
     * @param bool   $lang
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated since 1.2, use setLabel
     */
    public function set_label($label, $lang = true) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setLabel($label, $lang);
    }

    /**
     * @param string $url
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated since 1.1, use setAjaxUrl
     */
    public function set_ajax_url($url) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setAjaxUrl($url);
    }

    /**
     * @param string $target
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated since 1.1, use setTarget
     */
    public function set_target($target) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setTarget($target);
    }

    /**
     * @param bool $bool
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated 1.1
     */
    public function set_ajax($bool) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setAjax($bool);
    }

    /**
     * @param bool $bool
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated 1.1 use setNoPadding
     */
    public function set_nopadding($bool) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setNoPadding($bool);
    }

    /**
     * @param bool $bool
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated 1.1
     */
    public function set_active($bool) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setActive($bool);
    }

    /**
     * @param string $icon
     *
     * @return CElement_List_TabList_Tab
     *
     * @deprecated 1.1
     */
    public function set_icon($icon) {
        /** @var CElement_List_TabList_Tab $this */
        return $this->setIcon($icon);
    }
}
//@codingStandardsIgnoreEnd
