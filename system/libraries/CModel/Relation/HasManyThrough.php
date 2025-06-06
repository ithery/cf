<?php

use Illuminate\Contracts\Support\Arrayable;

class CModel_Relation_HasManyThrough extends CModel_Relation {
    use CModel_Relation_Trait_InteractsWithDictionary;

    /**
     * The "through" parent model instance.
     *
     * @var \CModel
     */
    protected $throughParent;

    /**
     * The far parent model instance.
     *
     * @var \CModel
     */
    protected $farParent;

    /**
     * The near key on the relationship.
     *
     * @var string
     */
    protected $firstKey;

    /**
     * The far key on the relationship.
     *
     * @var string
     */
    protected $secondKey;

    /**
     * The local key on the relationship.
     *
     * @var string
     */
    protected $localKey;

    /**
     * The local key on the intermediary model.
     *
     * @var string
     */
    protected $secondLocalKey;

    /**
     * Create a new has many through relationship instance.
     *
     * @param \CModel_Query $query
     * @param \CModel       $farParent
     * @param \CModel       $throughParent
     * @param string        $firstKey
     * @param string        $secondKey
     * @param string        $localKey
     * @param string        $secondLocalKey
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $farParent, CModel $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey) {
        $this->localKey = $localKey;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->farParent = $farParent;
        $this->throughParent = $throughParent;
        $this->secondLocalKey = $secondLocalKey;

        parent::__construct($query, $throughParent);
    }

    /**
     * Convert the relationship to a "has one through" relationship.
     *
     * @return \CModel_Relation_HasOneThrough
     */
    public function one() {
        return new CModel_Relation_HasOneThrough(
            $this->getQuery(),
            $this->farParent,
            $this->throughParent,
            $this->getFirstKeyName(),
            $this->secondKey,
            $this->getLocalKeyName(),
            $this->getSecondLocalKeyName(),
        );
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        $localValue = $this->farParent[$this->localKey];

        $this->performJoin();

        if (static::$constraints) {
            $this->query->where($this->getQualifiedFirstKeyName(), '=', $localValue);
        }
    }

    /**
     * Set the join clause on the query.
     *
     * @param null|CModel_Query $query
     *
     * @return void
     */
    protected function performJoin(CModel_Query $query = null) {
        $query = $query ?: $this->query;

        $farKey = $this->getQualifiedFarKeyName();

        $query->join($this->throughParent->getTable(), $this->getQualifiedParentKeyName(), '=', $farKey);

        if ($this->throughParentSoftDeletes()) {
            $query->withGlobalScope('SoftDeletableHasManyThrough', function ($query) {
                $query->where($this->throughParent->getQualifiedStatusColumn(), '>', 0);
            });
        }
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName() {
        return $this->parent->qualifyColumn($this->secondLocalKey);
    }

    /**
     * Determine whether "through" parent of the relation uses Soft Deletes.
     *
     * @return bool
     */
    public function throughParentSoftDeletes() {
        return in_array(CModel_SoftDelete_SoftDeleteTrait::class, c::classUsesRecursive(
            $this->throughParent
        ));
    }

    /**
     * Indicate that trashed "through" parents should be included in the query.
     *
     * @return $this
     */
    public function withTrashedParents() {
        $this->query->withoutGlobalScope('SoftDeletableHasManyThrough');

        return $this;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $whereIn = $this->whereInMethod($this->farParent, $this->localKey);

        $this->query->{$whereIn}(
            $this->getQualifiedFirstKeyName(),
            $this->getKeys($models, $this->localKey)
        );
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param array  $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     *
     * @return array
     */
    public function match(array $models, CModel_Collection $results, $relation) {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->getAttribute($this->localKey)])) {
                $model->setRelation(
                    $relation,
                    $this->related->newCollection($dictionary[$key])
                );
            }
        }

        return $models;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param \CModel_Collection $results
     *
     * @return array
     */
    protected function buildDictionary(CModel_Collection $results) {
        $dictionary = [];

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result) {
            $dictionary[$result->model_through_key][] = $result;
        }

        return $dictionary;
    }

    /**
     * Get the first related model record matching the attributes or instantiate it.
     *
     * @param array $attributes
     *
     * @return \CModel
     */
    public function firstOrNew(array $attributes) {
        if (is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->related->newInstance($attributes);
        }

        return $instance;
    }

    /**
     * Get the first record matching the attributes. If the record is not found, create it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \CModel
     */
    public function firstOrCreate(array $attributes = [], array $values = []) {
        if (!is_null($instance = (clone $this)->where($attributes)->first())) {
            return $instance;
        }

        return $this->createOrFirst(array_merge($attributes, $values));
    }

    /**
     * Attempt to create the record. If a unique constraint violation occurs, attempt to find the matching record.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \CModel
     */
    public function createOrFirst(array $attributes = [], array $values = []) {
        try {
            return $this->getQuery()->withSavepointIfNeeded(function () use ($attributes, $values) {
                return $this->create(array_merge($attributes, $values));
            });
        } catch (CDatabase_Exception_UniqueConstraintViolationException $exception) {
            if ($result = $this->where($attributes)->first()) {
                return $result;
            }

            throw $exception;
        }
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \CModel
     */
    public function updateOrCreate(array $attributes, array $values = []) {
        $instance = $this->firstOrNew($attributes);

        $instance->fill($values)->save();

        return $instance;
    }

    /**
     * Add a basic where clause to the query, and return the first result.
     *
     * @param \Closure|string|array $column
     * @param mixed                 $operator
     * @param mixed                 $value
     * @param string                $boolean
     *
     * @return \CModel|static
     */
    public function firstWhere($column, $operator = null, $value = null, $boolean = 'and') {
        return $this->where($column, $operator, $value, $boolean)->first();
    }

    /**
     * Execute the query and get the first related model.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*']) {
        $results = $this->take(1)->get($columns);

        return count($results) > 0 ? $results->first() : null;
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param array $columns
     *
     * @throws CModel_Exception_ModelNotFoundException
     *
     * @return CModel|static
     */
    public function firstOrFail($columns = ['*']) {
        if (!is_null($model = $this->first($columns))) {
            return $model;
        }

        throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($this->related));
    }

    /**
     * Execute the query and get the first result or call a callback.
     *
     * @param \Closure|array $columns
     * @param null|\Closure  $callback
     *
     * @return \CModel|static|mixed
     */
    public function firstOr($columns = ['*'], Closure $callback = null) {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        if (!is_null($model = $this->first($columns))) {
            return $model;
        }

        return $callback();
    }

    /**
     * Find a related model by its primary key.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return null|\CModel|\CModel_Collection
     */
    public function find($id, $columns = ['*']) {
        if (is_array($id) || $id instanceof Arrayable) {
            return $this->findMany($id, $columns);
        }

        return $this->where(
            $this->getRelated()->getQualifiedKeyName(),
            '=',
            $id
        )->first($columns);
    }

    /**
     * Find multiple related models by their primary keys.
     *
     * @param mixed $ids
     * @param array $columns
     *
     * @return \CModel_Collection
     */
    public function findMany($ids, $columns = ['*']) {
        $ids = $ids instanceof Arrayable ? $ids->toArray() : $ids;
        if (empty($ids)) {
            return $this->getRelated()->newCollection();
        }

        return $this->whereIn(
            $this->getRelated()->getQualifiedKeyName(),
            $ids
        )->get($columns);
    }

    /**
     * Find a related model by its primary key or throw an exception.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @throws \CModel_Exception_ModelNotFoundException
     *
     * @return \CModel|\CModel_Collection
     */
    public function findOrFail($id, $columns = ['*']) {
        $result = $this->find($id, $columns);

        $id = $id instanceof Arrayable ? $id->toArray() : $id;
        if (is_array($id)) {
            if (count($result) == count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($this->related), $id);
    }

    /**
     * Find a related model by its primary key or call a callback.
     *
     * @param mixed          $id
     * @param \Closure|array $columns
     * @param null|\Closure  $callback
     *
     * @return \CModel|\CModel_Collection|mixed
     */
    public function findOr($id, $columns = ['*'], Closure $callback = null) {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        $result = $this->find($id, $columns);

        $id = $id instanceof Arrayable ? $id->toArray() : $id;

        if (is_array($id)) {
            if (count($result) === count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        return $callback();
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return !is_null($this->farParent->{$this->localKey})
            ? $this->get()
            : $this->related->newCollection();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return CModel_Collection
     */
    public function get($columns = ['*']) {
        $builder = $this->prepareQueryBuilder($columns);

        $models = $builder->getModels();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param int    $perPage
     * @param array  $columns
     * @param string $pageName
     * @param int    $page
     *
     * @return CPagination_LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
        $this->query->addSelect($this->shouldSelect($columns));

        return $this->query->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int      $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param null|int $page
     *
     * @return CPagination_Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
        $this->query->addSelect($this->shouldSelect($columns));

        return $this->query->simplePaginate($perPage, $columns, $pageName, $page);
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
        $this->query->addSelect($this->shouldSelect($columns));

        return $this->query->cursorPaginate($perPage, $columns, $cursorName, $cursor);
    }

    /**
     * Set the select clause for the relation query.
     *
     * @param array $columns
     *
     * @return array
     */
    protected function shouldSelect(array $columns = ['*']) {
        if ($columns == ['*']) {
            $columns = [$this->related->getTable() . '.*'];
        }

        return array_merge($columns, [$this->getQualifiedFirstKeyName() . ' as model_through_key']);
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
        return $this->prepareQueryBuilder()->chunk($count, $callback);
    }

    /**
     * Chunk the results of a query by comparing numeric IDs.
     *
     * @param int         $count
     * @param callable    $callback
     * @param null|string $column
     * @param null|string $alias
     *
     * @return bool
     */
    public function chunkById($count, callable $callback, $column = null, $alias = null) {
        if ($column == null) {
            $column = $this->getRelated()->getQualifiedKeyName();
        }
        if ($alias == null) {
            $alias = $this->getRelated()->getKeyName();
        }

        return $this->prepareQueryBuilder()->chunkById($count, $callback, $column, $alias);
    }

    /**
     * Get a generator for the given query.
     *
     * @return \Generator
     */
    public function cursor() {
        return $this->prepareQueryBuilder()->cursor();
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param callable $callback
     * @param int      $count
     *
     * @return bool
     */
    public function each(callable $callback, $count = 1000) {
        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }
        });
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param int $chunkSize
     *
     * @return \CCollection_LazyCollection
     */
    public function lazy($chunkSize = 1000) {
        return $this->prepareQueryBuilder()->lazy($chunkSize);
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs.
     *
     * @param int         $chunkSize
     * @param null|string $column
     * @param null|string $alias
     *
     * @return \CCollection_LazyCollection
     */
    public function lazyById($chunkSize = 1000, $column = null, $alias = null) {
        if ($column == null) {
            $column = $this->getRelated()->getQualifiedKeyName();
        }
        if ($alias == null) {
            $alias = $this->getRelated()->getKeyName();
        }

        return $this->prepareQueryBuilder()->lazyById($chunkSize, $column, $alias);
    }

    /**
     * Prepare the query builder for query execution.
     *
     * @param array $columns
     *
     * @return \CModel_Query
     */
    protected function prepareQueryBuilder($columns = ['*']) {
        $builder = $this->query->applyScopes();

        return $builder->addSelect(
            $this->shouldSelect($builder->getQuery()->columns ? [] : $columns)
        );
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        if ($parentQuery->getQuery()->from === $this->throughParent->getTable()) {
            return $this->getRelationExistenceQueryForThroughSelfRelation($query, $parentQuery, $columns);
        }

        $this->performJoin($query);

        return $query->select($columns)->whereColumn(
            $this->getQualifiedLocalKeyName(),
            '=',
            $this->getQualifiedFirstKeyName()
        );
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQueryForSelfRelation(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        $query->from($query->getModel()->getTable() . ' as ' . $hash = $this->getRelationCountHash());

        $query->join($this->throughParent->getTable(), $this->getQualifiedParentKeyName(), '=', $hash . '.' . $this->secondLocalKey);

        if ($this->throughParentSoftDeletes()) {
            $query->where($this->throughParent->getQualifiedStatusColumn(), '>', 0);
        }

        $query->getModel()->setTable($hash);

        return $query->select($columns)->whereColumn(
            $parentQuery->getQuery()->from . '.' . $this->localKey,
            '=',
            $this->getQualifiedFirstKeyName()
        );
    }

    /**
     * Add the constraints for a relationship query on the same table as the through parent.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQueryForThroughSelfRelation(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        $table = $this->throughParent->getTable() . ' as ' . $hash = $this->getRelationCountHash();

        $query->join($table, $hash . '.' . $this->secondLocalKey, '=', $this->getQualifiedFarKeyName());

        if ($this->throughParentSoftDeletes()) {
            $query->where($this->throughParent->getQualifiedStatusColumn(), '>', 0);
        }

        return $query->select($columns)->whereColumn(
            $parentQuery->getQuery()->from . '.' . $this->localKey,
            '=',
            $hash . '.' . $this->firstKey
        );
    }

    /**
     * Get the qualified foreign key on the related model.
     *
     * @return string
     */
    public function getQualifiedFarKeyName() {
        return $this->getQualifiedForeignKeyName();
    }

    /**
     * Get the foreign key on the "through" model.
     *
     * @return string
     */
    public function getFirstKeyName() {
        return $this->firstKey;
    }

    /**
     * Get the qualified foreign key on the "through" model.
     *
     * @return string
     */
    public function getQualifiedFirstKeyName() {
        return $this->throughParent->qualifyColumn($this->firstKey);
    }

    /**
     * Get the foreign key on the related model.
     *
     * @return string
     */
    public function getForeignKeyName() {
        return $this->secondKey;
    }

    /**
     * Get the qualified foreign key on the related model.
     *
     * @return string
     */
    public function getQualifiedForeignKeyName() {
        return $this->related->qualifyColumn($this->secondKey);
    }

    /**
     * Get the local key on the far parent model.
     *
     * @return string
     */
    public function getLocalKeyName() {
        return $this->localKey;
    }

    /**
     * Get the qualified local key on the far parent model.
     *
     * @return string
     */
    public function getQualifiedLocalKeyName() {
        return $this->farParent->qualifyColumn($this->localKey);
    }

    /**
     * Get the local key on the intermediary model.
     *
     * @return string
     */
    public function getSecondLocalKeyName() {
        return $this->secondLocalKey;
    }
}
