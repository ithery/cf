<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_Widget
 */

// @codingStandardsIgnoreStart
trait CTrait_Compat_Element_Widget {
    /**
     * @param string     $title
     * @param bool|array $lang
     *
     * @return CElement_Component_Widget
     *
     * @deprecated since version 1.1, please Use setTitle
     */
    public function set_title($title, $lang = true) {
        /** @var CElement_Component_Widget $this */
        return $this->setTitle($title, $lang);
    }

    /**
     * @param string $icon
     *
     * @return $this
     *
     * @deprecated 1.1
     */
    public function set_icon($icon) {
        /** @var CElement_Component_Widget $this */
        return $this->setIcon($icon);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated 1.1
     */
    public function set_nopadding($bool) {
        /** @var CElement_Component_Widget $this */
        return $this->setNoPadding($bool);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     *
     * @deprecated 1.1 please use addHeaderAction
     */
    public function add_header_action($id = '') {
        /** @var CElement_Component_Widget $this */
        return $this->addHeaderAction($id);
    }

    /**
     * @param string $style
     *
     * @return CElement_Component_Widget
     *
     * @deprecated 1.1
     */
    public function set_header_action_style($style) {
        /** @var CElement_Component_Widget $this */
        return $this->setHeaderActionStyle($style);
    }

    /**
     * @param string $id
     *
     * @return CElement_FormInput_Checkbox_Switcher
     *
     * @deprecated 1.1
     */
    public function add_switcher($id = '') {
        /** @var CElement_Component_Widget $this */
        return $this->addSwitcher($id);
    }

    /**
     * @return bool
     *
     * @deprecated 1.1
     */
    public function get_collapse() {
        /** @var CElement_Component_Widget $this */
        return $this->collapse;
    }

    /**
     * @return bool
     *
     * @deprecated 1.1
     */
    public function get_close() {
        /** @var CElement_Component_Widget $this */
        return $this->close;
    }

    /**
     * @param bool $collapse
     * @param bool $js_collapse
     *
     * @return $this
     *
     * @deprecated 1.1, use setCollapse
     */
    public function set_collapse($collapse, $js_collapse = false) {
        /** @var CElement_Component_Widget $this */
        return $this->setCollapse($collapse, $js_collapse);
    }

    /**
     * @param bool $close
     *
     * @return $this
     *
     * @deprecated 1.1
     */
    public function set_close($close) {
        /** @var CElement_Component_Widget $this */
        $this->close = $close;

        return $this;
    }

    /**
     * @param string $height
     *
     * @return $this
     */
    public function set_height($height) {
        /** @var CElement_Component_Widget $this */
        $this->height = $height;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function set_scroll($bool) {
        /** @var CElement_Component_Widget $this */
        $this->scroll = $bool;

        return $this;
    }
}
// @codingStandardsIgnoreEnd
