<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 9:18:22 PM
 * @see CElement_FormInput_Select
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Select {
    /**
     * @deprecated since version 1.2
     *
     * @param mixed $list
     */
    public function set_list($list) {
        /** @var CElement_FormInput_Select $this */
        return $this->setList($list);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_multiple($bool) {
        /** @var CElement_FormInput_Select $this */
        return $this->setMultiple($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $applyjs
     */
    public function set_applyjs($applyjs) {
        /** @var CElement_FormInput_Select $this */
        return $this->setApplyJs($applyjs);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_hide_search($bool) {
        /** @var CElement_FormInput_Select $this */
        return $this->setHideSearch($bool);
    }

    /**
     * @deprecated since version 1.2
     */
    public function get_hide_search() {
        /** @var CElement_FormInput_Select $this */
        return $this->hide_search;
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $group
     * @param mixed $list
     */
    public function add_group_list($group, $list) {
        /** @var CElement_FormInput_Select $this */
        $this->group_list[$group] = $list;

        return $this;
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $c
     */
    public function add_dropdown_class($c) {
        /** @var CElement_FormInput_Select $this */
        if (is_array($c)) {
            $this->dropdown_classes = array_merge($c, $this->dropdown_classes);
        } else {
            if ($this->bootstrap == '3.3') {
                $c = str_replace('span', 'col-md-', $c);
                $c = str_replace('row-fluid', 'row', $c);
            }
            $this->dropdown_classes[] = $c;
        }

        return $this;
    }
}
//@codingStandardsIgnoreEnd
