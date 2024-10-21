<?php

defined('SYSPATH') or die('No direct access allowed.');

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
