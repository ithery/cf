<?php

trait CElement_Component_DataTable_Trait_SearchTrait {
    public $searchPlaceholder = '';

    protected $quickSearch = false;

    protected $haveQuickSearchPlaceholder = true;

    protected $quickSearchPlaceholder = '';

    protected $initialSearch;

    protected $customSearchSelector;

    public function setQuickSearch($quickSearch) {
        $this->quickSearch = $quickSearch;

        return $this;
    }

    public function setHaveQuickSearchPlaceHolder($bool = true) {
        $this->haveQuickSearchPlaceholder = $bool;

        return $this;
    }

    public function setSearchPlaceholder($placeholder) {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    public function setQuickSearchPlaceholder($placeholder) {
        $this->quickSearchPlaceholder = $placeholder;

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
