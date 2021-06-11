<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:30:47 AM
 */
trait CTrait_Element_Behavior_Searchable {
    /**
     * @var bool
     */
    protected $searchable;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setSearchable($bool) {
        $this->searchable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSearchable() {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isSearchable() {
        return $this->searchable == true;
    }
}
