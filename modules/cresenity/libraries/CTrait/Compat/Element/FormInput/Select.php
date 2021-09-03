<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 9:18:22 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Select {
    /**
     * @deprecated since version 1.2
     *
     * @param mixed $list
     */
    public function set_list($list) {
        return $this->setList($list);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_multiple($bool) {
        return $this->setMultiple($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $applyjs
     */
    public function set_applyjs($applyjs) {
        return $this->setApplyJs($applyjs);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_hide_search($bool) {
        return $this->setHideSearch($bool);
    }

    /**
     * @deprecated since version 1.2
     */
    public function get_hide_search() {
        return $this->hide_search;
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $group
     * @param mixed $list
     */
    public function add_group_list($group, $list) {
        $this->group_list[$group] = $list;
        return $this;
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $c
     */
    public function add_dropdown_class($c) {
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
