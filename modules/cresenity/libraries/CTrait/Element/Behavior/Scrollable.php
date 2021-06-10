<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:30:41 AM
 */
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
