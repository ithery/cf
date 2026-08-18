<?php

defined('SYSPATH') or die('No direct access allowed.');

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
     * @param mixed $parent
     *
     * @return $this
     */
    public function set_parent($parent) {
        return $this->setParent($parent);
    }

    /**
     * @deprecated since version 1.2, please use function setVisibility
     *
     * @param mixed $bool
     *
     * @return $this
     */
    public function set_visibility($bool) {
        return $this->setVisibility($bool);
    }

    /**
     * @deprecated since version 1.2, please use function addJs
     *
     * @param mixed $js
     *
     * @return $this
     */
    public function add_js($js) {
        return $this->addJs($js);
    }

    /**
     * @deprecated since version 1.2, please use function regenerateId
     *
     * @param mixed $recursive
     *
     * @return $this
     */
    public function regenerate_id($recursive = false) {
        return $this->regenerateId($recursive);
    }
}
