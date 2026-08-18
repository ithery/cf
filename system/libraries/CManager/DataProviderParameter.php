<?php

class CManager_DataProviderParameter {
    protected $searchAndData = [];

    protected $searchOrData = [];

    protected $sortData = [];

    protected $page = -1;

    protected $perPage = -1;

    public function __construct($searchAndData = [], $searchOrData = [], $sortData = []) {
        $this->searchAndData = $searchAndData;
        $this->searchOrData = $searchOrData;
        $this->sortData = $sortData;
    }

    /**
     * @return array
     */
    public function getSearchAndData() {
        return $this->searchAndData;
    }

    /**
     * @return array
     */
    public function getSearchOrData() {
        return $this->searchOrData;
    }

    /**
     * @return array
     */
    public function getSortData() {
        return $this->sortData;
    }

    public function getPage() {
        return $this->page;
    }

    public function getPerPage() {
        return $this->perPage;
    }

    public function setForPagination($page, $perPage) {
        $this->page = $page;
        $this->perPage = $perPage;

        return $this;
    }
}
