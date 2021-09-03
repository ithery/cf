<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:55:05 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Tab {
    /**
     * @param mixed $label
     * @param mixed $lang
     *
     * @return CElement_Component_Tab
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_ajax_url($url) {
        return $this->setAjaxUrl($url);
    }

    /**
     * Undocumented function
     *
     * @param string $target
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_target($target) {
        return $this->setTarget($target);
    }

    /**
     * Undocumented function
     *
     * @param string $bool
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }

    /**
     * Undocumented function
     *
     * @param string $bool
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_nopadding($bool) {
        return $this->setNoPadding($bool);
    }

    /**
     * Undocumented function
     *
     * @param string $bool
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_active($bool) {
        return $this->setActive($bool);
    }

    /**
     * Undocumented function
     *
     * @param string $icon
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function set_icon($icon) {
        return $this->setIcon($icon);
    }

    /**
     * Undocumented function
     *
     * @param int $indent
     *
     * @return CElement_Component_Tab
     *
     * @deprecated 1.1
     */
    public function header_html($indent = 0) {
        return $this->headerHtml($indent);
    }
}
//@codingStandardsIgnoreEnd
