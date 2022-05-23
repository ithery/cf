<?php

abstract class CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    protected $searchAnd = [];

    protected $searchOr = [];

    protected $sort = [];

    /**
     * @var CElement_Depends_DependsOn[]
     */
    protected $callbacks = [];

    public function searchAnd(array $search) {
        $this->searchAnd = $search;
    }

    public function searchOr(array $search) {
        $this->searchOr = $search;
    }

    public function sort(array $sort) {
        $this->sort = $sort;
    }

    public function createParameter() {
        return new CManager_DataProviderParameter($this->searchAnd, $this->searchOr, $this->sort);
    }
}
