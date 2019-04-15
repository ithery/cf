<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Tab {

    /**
     * 
     * @param string $id
     * @return CElement_Component_Tab
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    public function set_ajax_url($url) {
        return $this->setAjaxUrl($url);
    }

    public function set_target($target) {
        return $this->setTarget($target);
    }

    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }

    public function set_nopadding($bool) {
        return $this->setNoPadding($bool);
    }

    public function set_active($bool) {
        return $this->setActive($bool);
    }

    public function set_icon($icon) {
        return $this->setIcon($icon);
    }

    public function header_html($indent = 0) {
        return $this->headerHtml($indent);
    }

}
