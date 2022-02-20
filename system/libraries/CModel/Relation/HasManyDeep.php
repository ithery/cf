<?php

/**
 * @template TRelatedModel of \CModel
 * @extends \Illuminate\Database\Eloquent\Relations\Relation<TRelatedModel>
 */
class CModel_Relation_HasManyDeep extends CModel_Relation_HasManyThrough {
    use CModel_Relation_Trait_HasEagerLimit;
    use CModel_Relation_Trait_RetrievesIntermediateTables;

    /**
     * The "through" parent model instances.
     *
     * @var \CModel[]
     */
    protected $throughParents;

    /**
     * The foreign keys on the relationship.
     *
     * @var array
     */
    protected $foreignKeys;

    /**
     * The local keys on the relationship.
     *
     * @var array
     */
    protected $localKeys;

    /**
     * Create a new has many deep relationship instance.
     *
     * @param \CModel_Query $query
     * @param \CModel       $farParent
     * @param \CModel[]     $throughParents
     * @param array         $foreignKeys
     * @param array         $localKeys
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $farParent, array $throughParents, array $foreignKeys, array $localKeys) {
        $this->throughParents = $throughParents;
        $this->foreignKeys = $foreignKeys;
        $this->localKeys = $localKeys;

        $firstKey = is_array($foreignKeys[0]) ? $foreignKeys[0][1] : $foreignKeys[0];

        parent::__construct($query, $farParent, $throughParents[0], $firstKey, $foreignKeys[1], $localKeys[0], $localKeys[1]);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        parent::addConstraints();

        if (static::$constraints) {
            if (is_array($this->foreignKeys[0])) {
                $column = $this->throughParent->qualifyColumn($this->foreignKeys[0][0]);

                $this->query->where($column, '=', $this->farParent->getMorphClass());
            }
        }
    }

    /**
     * Set the join clauses on the query.
     *
     * @param null|\CModel_Query $query
     *
     * @return void
     */
    protected function performJoin(CModel_Query $query = null) {
        $query = $query ?: $this->query;

        $throughParents = array_reverse($this->throughParents);
        $foreignKeys = array_reverse($this->foreignKeys);
        $localKeys = array_reverse($this->localKeys);

        $segments = explode(' as ', $query->getQuery()->from);

        $alias = $segments[1] ?? null;

        foreach ($throughParents as $i => $throughParent) {
            $predecessor = $throughParents[$i - 1] ?? $this->related;

            $prefix = $i === 0 && $alias ? $alias . '.' : '';

            $this->joinThroughParent($query, $throughParent, $predecessor, $foreignKeys[$i], $localKeys[$i], $prefix);
        }
    }

    /**
     * Join a through parent table.
     *
     * @param \CModel_Query $query
     * @param \CModel       $throughParent
     * @param \CModel       $predecessor
     * @param array|string  $foreignKey
     * @param array|string  $localKey
     * @param string        $prefix
     *
     * @return void
     */
    protected function joinThroughParent(CModel_Query $query, CModel $throughParent, CModel $predecessor, $foreignKey, $localKey, $prefix) {
        if (is_array($localKey)) {
            $query->where($throughParent->qualifyColumn($localKey[0]), '=', $predecessor->getMorphClass());

            $localKey = $localKey[1];
        }

        $first = $throughParent->qualifyColumn($localKey);

        if (is_array($foreignKey)) {
            $query->where($predecessor->qualifyColumn($foreignKey[0]), '=', $throughParent->getMorphClass());

            $foreignKey = $foreignKey[1];
        }

        $second = $predecessor->qualifyColumn($prefix . $foreignKey);

        $query->join($throughParent->getTable(), $first, '=', $second);

        if ($this->throughParentInstanceSoftDeletes($throughParent)) {
            $column = $throughParent->getQualifiedDeletedAtColumn();

            $query->withGlobalScope(__CLASS__ . ":${column}", function (CModel_Query $query) use ($column) {
                $query->whereNull($column);
            });
        }
    }

    /**
     * Determine whether a "through" parent instance of the relation uses SoftDeletes.
     *
     * @param \CModel $instance
     *
     * @return bool
     */
    public function throughParentInstanceSoftDeletes(CModel $instance) {
        return in_array(CModel_SoftDelete_SoftDeleteTrait::class, c::classUsesRecursive($instance));
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        parent::addEagerConstraints($models);

        if (is_array($this->foreignKeys[0])) {
            $column = $this->throughParent->qualifyColumn($this->foreignKeys[0][0]);

            $this->query->where($column, '=', $this->farParent->getMorphClass());
        }
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*']) {
        $models = parent::get($columns);

        $this->hydrateIntermediateRelations($models->all());

        return $models;
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param int    $perPage
     * @param array  $columns
     * @param string $pageName
     * @param int    $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
        $columns = $this->shouldSelect($columns);

        $columns = array_diff($columns, [$this->getQualifiedFirstKeyName() . ' as laravel_through_key']);

        $this->query->addSelect($columns);

        return c::tap($this->query->paginate($perPage, $columns, $pageName, $page), function (CPagination_PaginatorInterface $paginator) {
            $this->hydrateIntermediateRelations($paginator->items());
        });
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int      $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param null|int $page
     *
     * @return \CPagination_PaginatorInterface
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
        $columns = $this->shouldSelect($columns);

        $columns = array_diff($columns, [$this->getQualifiedFirstKeyName() . ' as laravel_through_key']);

        $this->query->addSelect($columns);

        return c::tap($this->query->simplePaginate($perPage, $columns, $pageName, $page), function (CPagination_PaginatorInterface $paginator) {
            $this->hydrateIntermediateRelations($paginator->items());
        });
    }

    /**
     * Paginate the given query into a cursor paginator.
     *
     * @param null|int    $perPage
     * @param array       $columns
     * @param string      $cursorName
     * @param null|string $cursor
     *
     * @return \CPagination_CursorPaginatorInterface
     */
    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null) {
        $columns = $this->shouldSelect($columns);

        $columns = array_diff($columns, [$this->getQualifiedFirstKeyName() . ' as laravel_through_key']);

        $this->query->addSelect($columns);

        return c::tap($this->query->cursorPaginate($perPage, $columns, $cursorName, $cursor), function (CPagination_CursorPaginatorInterface $paginator) {
            $this->hydrateIntermediateRelations($paginator->items());
        });
    }

    /**
     * Set the select clause for the relation query.
     *
     * @param array $columns
     *
     * @return array
     */
    protected function shouldSelect(array $columns = ['*']) {
        return array_merge(parent::shouldSelect($columns), $this->intermediateColumns());
    }

    /**
     * Chunk the results of the query.
     *
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk($count, callable $callback) {
        return $this->prepareQueryBuilder()->chunk($count, function (CCollection $results) use ($callback) {
            $this->hydrateIntermediateRelations($results->all());

            return $callback($results);
        });
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param \CModel_Query $query
     * @param \CModel_Query $parentQuery
     * @param array|mixed   $columns
     *
     * @return \CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        foreach ($this->throughParents as $throughParent) {
            if ($throughParent->getTable() === $parentQuery->getQuery()->from) {
                if (!in_array(CModel_Relation_Trait_HasTableAlias::class, c::classUsesRecursive($throughParent))) {
                    $traitClass = CModel_Relation_Trait_HasTableAlias::class;
                    $parentClass = get_class($throughParent);

                    throw new Exception(
                        <<<EOT
This query requires an additional trait. Please add the ${traitClass} trait to ${parentClass}.
EOT
                    );
                }

                $table = $throughParent->getTable() . ' as ' . $this->getRelationCountHash();

                $throughParent->setTable($table);

                break;
            }
        }

        $query = parent::getRelationExistenceQuery($query, $parentQuery, $columns);

        if (is_array($this->foreignKeys[0])) {
            $column = $this->throughParent->qualifyColumn($this->foreignKeys[0][0]);

            $query->where($column, '=', $this->farParent->getMorphClass());
        }

        return $query;
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param \CModel_Query $query
     * @param \CModel_Query $parentQuery
     * @param array|mixed   $columns
     *
     * @return \CModel_Query
     */
    public function getRelationExistenceQueryForSelfRelation(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        $hash = $this->getRelationCountHash();

        $query->from($query->getModel()->getTable() . ' as ' . $hash);

        $this->performJoin($query);

        $query->getModel()->setTable($hash);

        return $query->select($columns)->whereColumn(
            $parentQuery->getQuery()->from . '.' . $this->localKey,
            '=',
            $this->getQualifiedFirstKeyName()
        );
    }

    /**
     * Restore soft-deleted models.
     *
     * @param array|string ...$columns
     *
     * @return $this
     */
    public function withTrashed(...$columns) {
        if (empty($columns)) {
            $this->query->withTrashed();

            return $this;
        }

        if (is_array($columns[0])) {
            $columns = $columns[0];
        }

        foreach ($columns as $column) {
            $this->query->withoutGlobalScope(__CLASS__ . ":${column}");
        }

        return $this;
    }

    /**
     * Get the "through" parent model instances.
     *
     * @return \CModel[]
     */
    public function getThroughParents() {
        return $this->throughParents;
    }

    /**
     * Get the foreign keys on the relationship.
     *
     * @return array
     */
    public function getForeignKeys() {
        return $this->foreignKeys;
    }

    /**
     * Get the local keys on the relationship.
     *
     * @return array
     */
    public function getLocalKeys() {
        return $this->localKeys;
    }
}
