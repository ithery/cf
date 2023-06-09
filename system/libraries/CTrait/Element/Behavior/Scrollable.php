<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Behavior_Scrollable {
    /**
     * @var bool
     */
    protected $scrollable;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setScrollable($bool) {
        $this->scrollable = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function getScrollable() {
        return $this->scrollable;
    }

    /**
     * @return bool
     */
    public function isScrollable() {
        return $this->scrollable == true;
    }
}
