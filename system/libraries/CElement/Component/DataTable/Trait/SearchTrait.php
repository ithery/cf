<?php

trait CElement_Component_DataTable_Trait_SearchTrait {
    protected $quick_search = false;

    public $searchPlaceholder = '';

    protected $initialSearch;

    protected $customSearchSelector;

    public function setQuickSearch($quick_search) {
        $this->quick_search = $quick_search;
        return $this;
    }

    public function setSearchPlaceholder($placeholder) {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    public function setInitialSearch($initialSearch) {
        $this->initialSearch = $initialSearch;
        return $this;
    }

    /**
     * @param string|CElement $selector
     *
     * @return $this
     */
    public function setCustomSearchSelector($selector) {
        if ($selector instanceof CElement) {
            $selector = '#' . $selector->id();
        }
        $this->customSearchSelector = $selector;
        return $this;
    }
}
