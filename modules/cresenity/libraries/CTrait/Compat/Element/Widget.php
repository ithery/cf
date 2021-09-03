<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:55:05 AM
 */

// @codingStandardsIgnoreStart
trait CTrait_Compat_Element_Widget {
    /**
     * @param mixed $title
     * @param mixed $lang
     *
     * @deprecated since version 1.1, please Use setTitle
     *
     * @return CElement_Component_Widget
     */
    public function set_title($title, $lang = true) {
        return $this->setTitle($title, $lang);
    }

    /**
     * @param type $icon
     *
     * @return $this
     *
     * @deprecated 1.1
     */
    public function set_icon($icon) {
        return $this->setIcon($icon);
    }

    /**
     * Undocumented function
     *
     * @param [type] $bool
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function set_nopadding($bool) {
        return $this->setNoPadding($bool);
    }

    /**
     * Undocumented function
     *
     * @param string $id
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function add_header_action($id = '') {
        return $this->addHeaderAction($id);
    }

    /**
     * Undocumented function
     *
     * @param [type] $style
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function set_header_action_style($style) {
        return $this->setHeaderActionStyle($style);
    }

    /**
     * Undocumented function
     *
     * @param string $id
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function add_switcher($id = '') {
        return $this->addSwitcher($id);
    }

    /**
     * Undocumented function
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function get_collapse() {
        return $this->collapse;
    }

    /**
     * Undocumented function
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function get_close() {
        return $this->close;
    }

    /**
     * Undocumented function
     *
     * @param [type]  $collapse
     * @param boolean $js_collapse
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function set_collapse($collapse, $js_collapse = false) {
        $this->collapse = $collapse;
        $this->js_collapse = $js_collapse;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $close
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function set_close($close) {
        $this->close = $close;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $height
     *
     * @return void
     */
    public function set_height($height) {
        $this->height = $height;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $bool
     *
     * @return void
     */
    public function set_scroll($bool) {
        $this->scroll = $bool;
        return $this;
    }
}
// @codingStandardsIgnoreEnd
