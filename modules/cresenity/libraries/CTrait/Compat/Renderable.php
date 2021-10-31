<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:40:40 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Renderable {
    /**
     * @deprecated since version 1.2, please use function childCount
     *
     * @return int
     */
    public function child_count() {
        return $this->childCount();
    }

    /**
     * @deprecated since version 1.2, please use function setParent
     *
     * @return $this
     *
     * @param mixed $parent
     */
    public function set_parent($parent) {
        return $this->setParent($parent);
    }

    /**
     * @deprecated since version 1.2, please use function setVisibility
     *
     * @return $this
     *
     * @param mixed $bool
     */
    public function set_visibility($bool) {
        return $this->setVisibility($bool);
    }

    /**
     * @deprecated since version 1.2, please use function addJs
     *
     * @return $this
     *
     * @param mixed $js
     */
    public function add_js($js) {
        return $this->addJs($js);
    }

    /**
     * @deprecated since version 1.2, please use function regenerateId
     *
     * @return $this
     *
     * @param mixed $recursive
     */
    public function regenerate_id($recursive = false) {
        return $this->regenerateId($recursive);
    }

    /**
     * @deprecated since version 1.2, please use function toArray
     *
     * @return array
     */
    public function toarray() {
        return $this->toArray();
    }
}
