<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:30:41 AM
 */
trait CTrait_Element_Behavior_Sortable {
    /**
     * @var bool
     */
    protected $sortable;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setSortable($bool) {
        $this->sortable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSortable() {
        return $this->sortable;
    }

    /**
     * @return bool
     */
    public function isSortable() {
        return $this->sortable == true;
    }
}
