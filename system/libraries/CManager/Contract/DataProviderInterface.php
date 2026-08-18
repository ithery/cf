<?php

interface CManager_Contract_DataProviderInterface {
    /**
     * @param array $searchData
     *
     * @return void
     */
    public function searchOr(array $searchData);

    /**
     * @param array $searchData
     *
     * @return void
     */
    public function searchAnd(array $searchData);

    /**
     * @param array $sortData
     *
     * @return void
     */
    public function sort(array $sortData);

    /**
     * Paginate the given query.
     *
     * @param int        $perPage
     * @param array      $columns
     * @param string     $pageName
     * @param null|int   $page
     * @param null|mixed $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return CPagination_LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null);

    /**
     * @return CInterface_Enumerable
     */
    public function toEnumerable();

    /**
     * @param string $method
     * @param string $column
     *
     * @return mixed
     */
    public function aggregate($method, $column);
}
