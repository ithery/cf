<?php

interface CModel_Scout_Contract_PaginateModel {
    /**
     * Paginate the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function paginate(CModel_Scout_Builder $builder, $perPage, $page);

    /**
     * Paginate the given search on the engine using simple pagination.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function simplePaginate(CModel_Scout_Builder $builder, $perPage, $page);
}
