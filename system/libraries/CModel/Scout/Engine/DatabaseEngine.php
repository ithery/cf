<?php

class CModel_Scout_Engine_DatabaseEngine extends CModel_Scout_EngineAbstract implements CModel_Scout_Contract_PaginateModel {
    /**
     * Create a new engine instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Update the given model in the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function update($models) {
    }

    /**
     * Remove the given model from the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function delete($models) {
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return mixed
     */
    public function search($builder) {
        $models = $this->searchModels($builder);

        return [
            'results' => $models,
            'total' => $models->count(),
        ];
    }

    /**
     * Paginate the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function paginate(CModel_Scout_Builder $builder, $perPage, $page) {
        return $this->buildSearchQuery($builder)
            ->when(!$this->getFullTextColumns($builder), function ($query) use ($builder) {
                $query->orderBy($builder->model->getKeyName(), 'desc');
            })
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Paginate the given search on the engine using simple pagination.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return \CPagination_PaginatorInterface
     */
    public function simplePaginate(CModel_Scout_Builder $builder, $perPage, $page) {
        return $this->buildSearchQuery($builder)
            ->when(!$this->getFullTextColumns($builder), function ($query) use ($builder) {
                $query->orderBy($builder->model->getKeyName(), 'desc');
            })
            ->simplePaginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get the Eloquent models for the given builder.
     *
     * @param \CModel_Scout_Builder $builder
     * @param null|int              $page
     * @param null|int              $perPage
     *
     * @return \CModel_Collection
     */
    protected function searchModels(CModel_Scout_Builder $builder, $page = null, $perPage = null) {
        return $this->buildSearchQuery($builder)
            ->when(!is_null($page) && !is_null($perPage), function ($query) use ($page, $perPage) {
                $query->forPage($page, $perPage);
            })
            ->when(!$this->getFullTextColumns($builder), function ($query) use ($builder) {
                $query->orderBy($builder->model->getKeyName(), 'desc');
            })
            ->get();
    }

    /**
     * Initialize / build the search query for the given Scout builder.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return \CDatabase_Query_Builder
     */
    protected function buildSearchQuery(CModel_Scout_Builder $builder) {
        $query = $this->initializeSearchQuery(
            $builder,
            array_keys($builder->model->toSearchableArray()),
            $this->getPrefixColumns($builder),
            $this->getFullTextColumns($builder)
        );

        return $this->constrainForSoftDeletes(
            $builder,
            $this->addAdditionalConstraints($builder, $query->take($builder->limit))
        );
    }

    /**
     * Build the initial text search database query for all relevant columns.
     *
     * @param \CModel_Scout_Builder $builder
     * @param array                 $columns
     * @param array                 $prefixColumns
     * @param array                 $fullTextColumns
     *
     * @return \CModel_Query
     */
    protected function initializeSearchQuery(CModel_Scout_Builder $builder, array $columns, array $prefixColumns = [], array $fullTextColumns = []) {
        if (c::blank($builder->query)) {
            return $builder->model->query();
        }

        return $builder->model->query()->where(function ($query) use ($builder, $columns, $prefixColumns, $fullTextColumns) {
            $connectionType = $builder->model->getConnection()->driverName();

            $canSearchPrimaryKey = ctype_digit($builder->query)
                                   && in_array($builder->model->getKeyType(), ['int', 'integer'])
                                   && ($connectionType != 'pgsql' || $builder->query <= PHP_INT_MAX)
                                   && in_array($builder->model->getKeyName(), $columns);

            if ($canSearchPrimaryKey) {
                $query->orWhere($builder->model->getQualifiedKeyName(), $builder->query);
            }

            $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

            foreach ($columns as $column) {
                if (in_array($column, $fullTextColumns)) {
                    $query->orWhereFullText(
                        $builder->model->qualifyColumn($column),
                        $builder->query,
                        $this->getFullTextOptions($builder)
                    );
                } else {
                    if ($canSearchPrimaryKey && $column === $builder->model->getKeyName()) {
                        continue;
                    }

                    $query->orWhere(
                        $builder->model->qualifyColumn($column),
                        $likeOperator,
                        in_array($column, $prefixColumns) ? $builder->query . '%' : '%' . $builder->query . '%',
                    );
                }
            }
        });
    }

    /**
     * Add additional, developer defined constraints to the search query.
     *
     * @param \CModel_Scout_Builder $builder
     * @param \CModel_Query         $query
     *
     * @return \CModel_Query
     */
    protected function addAdditionalConstraints(CModel_Scout_Builder $builder, $query) {
        return $query->when(!is_null($builder->callback), function ($query) use ($builder) {
            call_user_func($builder->callback, $query, $builder, $builder->query);
        })->when(!$builder->callback && count($builder->wheres) > 0, function ($query) use ($builder) {
            foreach ($builder->wheres as $key => $value) {
                if ($key !== '__soft_deleted') {
                    $query->where($key, '=', $value);
                }
            }
        })->when(!$builder->callback && count($builder->whereIns) > 0, function ($query) use ($builder) {
            foreach ($builder->whereIns as $key => $values) {
                $query->whereIn($key, $values);
            }
        })->when(!is_null($builder->queryCallback), function ($query) use ($builder) {
            call_user_func($builder->queryCallback, $query);
        });
    }

    /**
     * Ensure that soft delete constraints are properly applied to the query.
     *
     * @param \CModel_Scout_Builder                 $builder
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \CModel_Query
     */
    protected function constrainForSoftDeletes($builder, $query) {
        if (carr::get($builder->wheres, '__soft_deleted') === 0) {
            return $query->withoutTrashed();
        } elseif (carr::get($builder->wheres, '__soft_deleted') === 1) {
            return $query->onlyTrashed();
        } elseif (in_array(CModel_SoftDelete_SoftDeleteTrait::class, c::classUsesRecursive(get_class($builder->model)))
            && CF::config('model.scout.soft_delete', false)
        ) {
            return $query->withTrashed();
        }

        return $query;
    }

    /**
     * Get the full-text columns for the query.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return array
     */
    protected function getFullTextColumns(CModel_Scout_Builder $builder) {
        return $this->getAttributeColumns($builder, CModel_Scout_Attributes_SearchUsingFullText::class);
    }

    /**
     * Get the prefix search columns for the query.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return array
     */
    protected function getPrefixColumns(CModel_Scout_Builder $builder) {
        return $this->getAttributeColumns($builder, CModel_Scout_Attributes_SearchUsingPrefix::class);
    }

    /**
     * Get the columns marked with a given attribute.
     *
     * @param \CModel_Scout_Builder $builder
     * @param string                $attributeClass
     *
     * @return array
     */
    protected function getAttributeColumns(CModel_Scout_Builder $builder, $attributeClass) {
        $columns = [];

        if (PHP_MAJOR_VERSION < 8) {
            return [];
        }

        foreach ((new ReflectionMethod($builder->model, 'toSearchableArray'))->getAttributes() as $attribute) {
            if ($attribute->getName() !== $attributeClass) {
                continue;
            }

            $columns = array_merge($columns, carr::wrap($attribute->getArguments()[0]));
        }

        return $columns;
    }

    /**
     * Get the full-text search options for the query.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return array
     */
    protected function getFullTextOptions(CModel_Scout_Builder $builder) {
        $options = [];

        if (PHP_MAJOR_VERSION < 8) {
            return [];
        }

        foreach ((new ReflectionMethod($builder->model, 'toSearchableArray'))->getAttributes(CModel_Scout_Attributes_SearchUsingFullText::class) as $attribute) {
            $arguments = $attribute->getArguments()[1] ?? [];

            $options = array_merge($options, carr::wrap($arguments));
        }

        return $options;
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     *
     * @return \CCollection
     */
    public function mapIds($results) {
        $results = $results['results'];

        return count($results) > 0
                    ? c::collect($results->modelKeys())
                    : c::collect();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CModel_Collection
     */
    public function map(CModel_Scout_Builder $builder, $results, $model) {
        return $results['results'];
    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CCollection_LazyCollection
     */
    public function lazyMap(CModel_Scout_Builder $builder, $results, $model) {
        return new CCollection_LazyCollection($results['results']->all());
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     *
     * @return int
     */
    public function getTotalCount($results) {
        return $results['total'];
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function flush($model) {
    }

    /**
     * Create a search index.
     *
     * @param string $name
     * @param array  $options
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function createIndex($name, array $options = []) {
    }

    /**
     * Delete a search index.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function deleteIndex($name) {
    }
}
