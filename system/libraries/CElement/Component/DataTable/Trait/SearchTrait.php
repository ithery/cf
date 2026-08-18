<?php

trait CElement_Component_DataTable_Trait_SearchTrait {
    /**
     * @var string
     */
    public $searchPlaceholder = '';

    /**
     * @var bool
     */
    protected $quickSearch = false;

    /**
     * @var bool
     */
    protected $haveQuickSearchPlaceholder = true;

    /**
     * @var string
     */
    protected $quickSearchPlaceholder = '';

    /**
     * @var null|string
     */
    protected $initialSearch;

    /**
     * @var null|string
     */
    protected $customSearchSelector;

    /**
     * @param bool $quickSearch
     *
     * @return $this
     */
    public function setQuickSearch($quickSearch) {
        $this->quickSearch = $quickSearch;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHaveQuickSearchPlaceHolder($bool = true) {
        $this->haveQuickSearchPlaceholder = $bool;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     */
    public function setSearchPlaceholder($placeholder) {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     */
    public function setQuickSearchPlaceholder($placeholder) {
        $this->quickSearchPlaceholder = $placeholder;

        return $this;
    }

    /**
     * @param string $initialSearch
     *
     * @return $this
     */
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
