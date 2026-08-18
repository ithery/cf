<?php

interface CModel_Scout_Contract_PaginateModelUsingDatabase {
    /**
     * Paginate the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param string                $pageName
     * @param int                   $page
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function paginateUsingDatabase(CModel_Scout_Builder $builder, $perPage, $pageName, $page);

    /**
     * Paginate the given search on the engine using simple pagination.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param string                $pageName
     * @param int                   $page
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function simplePaginateUsingDatabase(CModel_Scout_Builder $builder, $perPage, $pageName, $page);
}
