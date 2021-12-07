<?php

abstract class CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    protected $search = [];

    protected $sort = [];

    public function search(array $search) {
        $this->search = $search;
    }

    public function sort(array $sort) {
        $this->sort = $sort;
    }
}
