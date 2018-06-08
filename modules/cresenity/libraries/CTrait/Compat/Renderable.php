<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:40:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Renderable {

    /**
     * 
     * @deprecated since version 1.2, please use function childCount
     * @return int
     */
    public function child_count() {
        return $this->childCount();
    }

    /**
     * 
     * @deprecated since version 1.2, please use function setParent
     * @return $this
     */
    public function set_parent($parent) {
        return $this->setParent($parent);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function setVisibility
     * @return $this
     */
    public function set_visibility($bool) {
        return $this->setVisibility($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function addJs
     * @return $this
     */
    public function add_js($js) {
        return $this->addJs($js);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function regenerateId
     * @return $this
     */
    public function regenerate_id($recursive = false) {
        return $this->regenerateId($recursive);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function toArray
     * @return array
     */
    public function toarray() {
        return $this->toArray();
    }

}
