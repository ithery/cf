<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Searchable {

    /**
     *
     * @var bool
     */
    protected $searchable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setSearchable($bool) {
        $this->searchable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getSearchable() {
        return $this->searchable;
    }

    /**
     * 
     * @return bool
     */
    public function isSearchable() {
        return $this->searchable == true;
    }

}
