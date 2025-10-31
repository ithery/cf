<?php
use Illuminate\Contracts\Support\Arrayable;

/**
 * @template TModel of \CModel
 */
class CModel_Scout_Builder {
    use CTrait_Macroable;
    use CTrait_Tappable;
    use CTrait_Conditionable;

    /**
     * The model instance.
     *
     * @var \CModel
     */
    public $model;

    /**
     * The query expression.
     *
     * @var string
     */
    public $query;

    /**
     * Optional callback before search execution.
     *
     * @var null|\Closure
     */
    public $callback;

    /**
     * Optional callback before model query execution.
     *
     * @var null|\Closure
     */
    public $queryCallback;

    /**
     * Optional callback after raw search.
     *
     * @var null|\Closure
     */
    public $afterRawSearchCallback;

    /**
     * The custom index specified for the search.
     *
     * @var string
     */
    public $index;

    /**
     * The "where" constraints added to the query.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * The "where in" constraints added to the query.
     *
     * @var array
     */
    public $whereIns = [];

    /**
     * The "where not in" constraints added to the query.
     *
     * @var array
     */
    public $whereNotIns = [];

    /**
     * The "limit" that should be applied to the search.
     *
     * @var int
     */
    public $limit;

    /**
     * The "order" that should be applied to the search.
     *
     * @var array
     */
    public $orders = [];

    /**
     * Extra options that should be applied to the search.
     *
     * @var array
     */
    public $options = [];

    /**
     * Create a new search builder instance.
     *
     * @param \CModel       $model
     * @param string        $query
     * @param null|\Closure $callback
     * @param bool          $softDelete
     *
     * @return void
     */
    public function __construct($model, $query, $callback = null, $softDelete = false) {
        $this->model = $model;
        $this->query = $query;
        $this->callback = $callback;

        if ($softDelete) {
            $this->wheres['__soft_deleted'] = 0;
        }
    }

    /**
     * Specify a custom index to perform this search on.
     *
     * @param string $index
     *
     * @return $this
     */
    public function within($index) {
        $this->index = $index;

        return $this;
    }

    /**
     * Add a constraint to the search query.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function where($field, $value) {
        $this->wheres[$field] = $value;

        return $this;
    }

    /**
     * Add a "where in" constraint to the search query.
     *
     * @param string                                        $field
     * @param \Illuminate\Contracts\Support\Arrayable|array $values
     *
     * @return $this
     */
    public function whereIn($field, $values) {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->whereIns[$field] = $values;

        return $this;
    }

    /**
     * Add a "where not in" constraint to the search query.
     *
     * @param string                                        $field
     * @param \Illuminate\Contracts\Support\Arrayable|array $values
     *
     * @return $this
     */
    public function whereNotIn($field, $values) {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $this->whereNotIns[$field] = $values;

        return $this;
    }

    /**
     * Include soft deleted records in the results.
     *
     * @return $this
     */
    public function withTrashed() {
        unset($this->wheres['__soft_deleted']);

        return $this;
    }

    /**
     * Include only soft deleted records in the results.
     *
     * @return $this
     */
    public function onlyTrashed() {
        return c::tap($this->withTrashed(), function () {
            $this->wheres['__soft_deleted'] = 1;
        });
    }

    /**
     * Set the "limit" for the search query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take($limit) {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add an "order" for the search query.
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc') {
        $this->orders[] = [
            'column' => $column,
            'direction' => strtolower($direction) == 'asc' ? 'asc' : 'desc',
        ];

        return $this;
    }

    /**
     * Add a descending "order by" clause to the search query.
     *
     * @param string $column
     *
     * @return $this
     */
    public function orderByDesc($column) {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     *
     * @return $this
     */
    public function latest($column = null) {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created';
        }

        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     *
     * @return $this
     */
    public function oldest($column = null) {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created';
        }

        return $this->orderBy($column, 'asc');
    }

    /**
     * Set extra options for the search query.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Set the callback that should have an opportunity to modify the database query.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function query($callback) {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Get the raw results of the search.
     *
     * @return mixed
     */
    public function raw() {
        return $this->engine()->search($this);
    }

    /**
     * Set the callback that should have an opportunity to inspect and modify the raw result returned by the search engine.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function withRawResults($callback) {
        $this->afterRawSearchCallback = $callback;

        return $this;
    }

    /**
     * Get the keys of search results.
     *
     * @return \CCollection
     */
    public function keys() {
        return $this->engine()->keys($this);
    }

    /**
     * Get the first result from the search.
     *
     * @return \CModel
     */
    public function first() {
        return $this->get()->first();
    }

    /**
     * Get the results of the search.
     *
     * @return \CModel_Collection
     */
    public function get() {
        return $this->engine()->get($this);
    }

    /**
     * Get the results of the search as a "lazy collection" instance.
     *
     * @return \CCollection_LazyCollection
     */
    public function cursor() {
        return $this->engine()->cursor($this);
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int      $perPage
     * @param string   $pageName
     * @param null|int $page
     *
     * @return \CPagination_PaginatorInterface
     */
    public function simplePaginate($perPage = null, $pageName = 'page', $page = null) {
        $engine = $this->engine();
        if ($engine instanceof CModel_Scout_Contract_PaginateModel) {
            return $engine->simplePaginate($this, $perPage, $page)->appends('query', $this->query);
        } elseif ($engine instanceof CModel_Scout_Contract_PaginateModelUsingDatabase) {
            return $engine->simplePaginateUsingDatabase($this, $perPage, $pageName, $page)->appends('query', $this->query);
        }
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = $this->model->newCollection($engine->map(
            $this,
            $this->applyAfterRawSearchCallback($rawResults = $engine->paginate($this, $perPage, $page)),
            $this->model
        )->all());

        $hasMorePages = ($perPage * $page) < $engine->getTotalCount($rawResults);

        $paginator = CContainer::getInstance()->makeWith(CPagination_Paginator::class, [
            'items' => $results,
            'perPage' => $perPage,
            'currentPage' => $page,
            'options' => [
                'path' => CPagination_Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ])->hasMorePagesWhen($hasMorePages);

        return $paginator->appends('query', $this->query);
    }

    /**
     * Paginate the given query into a simple paginator with raw data.
     *
     * @param int      $perPage
     * @param string   $pageName
     * @param null|int $page
     *
     * @return \CPagination_PaginatorInterface
     */
    public function simplePaginateRaw($perPage = null, $pageName = 'page', $page = null) {
        $engine = $this->engine();
        if ($engine instanceof CModel_Scout_Contract_PaginateModel) {
            return $engine->simplePaginate($this, $perPage, $page)->appends('query', $this->query);
        } elseif ($engine instanceof CModel_Scout_Contract_PaginateModelUsingDatabase) {
            return $engine->simplePaginateUsingDatabase($this, $perPage, $pageName, $page)->appends('query', $this->query);
        }
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = $this->applyAfterRawSearchCallback($engine->paginate($this, $perPage, $page));

        $hasMorePages = ($perPage * $page) < $engine->getTotalCount($results);

        $paginator = CContainer::getInstance()->makeWith(CPagination_Paginator::class, [
            'items' => $results,
            'perPage' => $perPage,
            'currentPage' => $page,
            'options' => [
                'path' => CPagination_Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ])->hasMorePagesWhen($hasMorePages);

        return $paginator->appends('query', $this->query);
    }

    /**
     * Paginate the given query into a paginator.
     *
     * @param int      $perPage
     * @param string   $pageName
     * @param null|int $page
     *
     * @return \CPagination_LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null) {
        $engine = $this->engine();
        if ($engine instanceof CModel_Scout_Contract_PaginateModel) {
            return $engine->simplePaginate($this, $perPage, $page)->appends('query', $this->query);
        } elseif ($engine instanceof CModel_Scout_Contract_PaginateModelUsingDatabase) {
            return $engine->simplePaginateUsingDatabase($this, $perPage, $pageName, $page)->appends('query', $this->query);
        }
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = $this->model->newCollection($engine->map(
            $this,
            $this->applyAfterRawSearchCallback($rawResults = $engine->paginate($this, $perPage, $page)),
            $this->model
        )->all());

        $paginator = CContainer::getInstance()->makeWith(CPagination_LengthAwarePaginator::class, [
            'items' => $results,
            'total' => $this->getTotalCount($rawResults),
            'perPage' => $perPage,
            'currentPage' => $page,
            'options' => [
                'path' => CPagination_Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ]);

        return $paginator->appends('query', $this->query);
    }

    /**
     * Paginate the given query into a simple paginator with raw data.
     *
     * @param int      $perPage
     * @param string   $pageName
     * @param null|int $page
     *
     * @return \CPagination_LengthAwarePaginator
     */
    public function paginateRaw($perPage = null, $pageName = 'page', $page = null) {
        $engine = $this->engine();
        if ($engine instanceof CModel_Scout_Contract_PaginateModel) {
            return $engine->simplePaginate($this, $perPage, $page)->appends('query', $this->query);
        } elseif ($engine instanceof CModel_Scout_Contract_PaginateModelUsingDatabase) {
            return $engine->simplePaginateUsingDatabase($this, $perPage, $pageName, $page)->appends('query', $this->query);
        }
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $ $results = $this->applyAfterRawSearchCallback($engine->paginate($this, $perPage, $page));

        $paginator = CContainer::getInstance()->makeWith(CPagination_LengthAwarePaginator::class, [
            'items' => $results,
            'total' => $this->getTotalCount($results),
            'perPage' => $perPage,
            'currentPage' => $page,
            'options' => [
                'path' => CPagination_Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ]);

        return $paginator->appends('query', $this->query);
    }

    /**
     * Get the total number of results from the Scout engine, or fallback to query builder.
     *
     * @param mixed $results
     *
     * @return int
     */
    protected function getTotalCount($results) {
        $engine = $this->engine();

        $totalCount = $engine->getTotalCount($results);

        if (is_null($this->queryCallback)) {
            return $totalCount;
        }
        $ids = $engine->mapIdsFrom($results, $this->model->getScoutKeyName())->all();

        if (count($ids) < $totalCount) {
            $ids = $engine->keys(c::tap(clone $this, function ($builder) use ($totalCount) {
                $builder->take(
                    is_null($this->limit) ? $totalCount : min($this->limit, $totalCount)
                );
            }))->all();
        }

        return $this->model->queryScoutModelsByIds(
            $this,
            $ids
        )->toBase()->getCountForPagination();
    }

    /**
     * Invoke the "after raw search" callback.
     *
     * @param mixed $results
     *
     * @return mixed
     */
    public function applyAfterRawSearchCallback($results) {
        if ($this->afterRawSearchCallback) {
            $results = call_user_func($this->afterRawSearchCallback, $results) ?: $results;
        }

        return $results;
    }

    /**
     * Get the engine that should handle the query.
     *
     * @return mixed
     */
    protected function engine() {
        return $this->model->searchableUsing();
    }

    /**
     * Get the connection type for the underlying model.
     */
    public function modelConnectionType(): string {
        return $this->model->getConnection()->getDriverName();
    }
}
