<?php

interface CManager_Contract_DataProviderInterface {
    public function searchOr(array $searchData);

    public function searchAnd(array $searchData);

    public function sort(array $sortData);

    /**
     * Paginate the given query.
     *
     * @param int      $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param null|int $page
     *
     * @throws \InvalidArgumentException
     *
     * @return CPagination_LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * @return CInterface_Enumerable
     */
    public function toEnumerable();
}
