<?php

/**
 * @mixin CModel_Query
 *
 * @template TRelatedModel of \CModel
 *
 * @method mixed                                value($column)                                                                                                                Get a single column's value from the first result of a query.
 * @method mixed                                pluck($column)                                                                                                                Get a single column's value from the first result of a query.
 * @method void                                 chunk($count, callable $callback)                                                                                             Chunk the results of the query.
 * @method \CCollection                         lists($column, $key = null)                                                                                                   Get an array with the values of a given column.
 * @method void                                 onDelete(Closure $callback)                                                                                                   Register a replacement for the default delete function.
 * @method CModel[]                             getModels($columns = ['*'])                                                                                                   Get the hydrated models without eager loading.
 * @method array                                eagerLoadRelations(array $models)                                                                                             Eager load the relationships for the models.
 * @method array                                loadRelation(array $models, $name, Closure $constraints)                                                                      Eagerly load the relationship on a set of models.
 * @method static                               CModel_Query<TRelatedModel>|static   where($column, $operator = null, $value = null, $boolean = 'and')                        Add a basic where clause to the query.
 * @method static                               CModel_Query<TRelatedModel>|static   whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)              Add a relationship count / exists condition to the query with where clauses.
 * @method static                               CModel_Query<TRelatedModel>|static   orWhere($column, $operator = null, $value = null)                                        Add an "or where" clause to the query.
 * @method static                               CModel_Query<TRelatedModel>|static   has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) Add a relationship count condition to the query.
 * @method static                               CModel_Query<TRelatedModel>|static   whereRaw($sql, array $bindings = [])
 * @method static                               CModel_Query<TRelatedModel>|static   whereBetween($column, array $values)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNotBetween($column, array $values)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNested(Closure $callback)
 * @method static                               CModel_Query<TRelatedModel>|static   addNestedWhereQuery($query)
 * @method static                               CModel_Query<TRelatedModel>|static   whereExists(Closure $callback)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNotExists(Closure $callback)
 * @method static                               CModel_Query<TRelatedModel>|static   whereIn($column, $values)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNotIn($column, $values)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNull($column)
 * @method static                               CModel_Query<TRelatedModel>|static   whereNotNull($column)
 * @method CModel_Query<TRelatedModel>|static   orWhereRaw($sql, array $bindings = [])
 * @method CModel_Query<TRelatedModel>|static   orWhereBetween($column, array $values)
 * @method CModel_Query<TRelatedModel>|static   orWhereNotBetween($column, array $values)
 * @method CModel_Query<TRelatedModel>|static   orWhereExists(Closure $callback)
 * @method CModel_Query<TRelatedModel>|static   orWhereNotExists(Closure $callback)
 * @method CModel_Query<TRelatedModel>|static   orWhereIn($column, $values)
 * @method CModel_Query<TRelatedModel>|static   orWhereNotIn($column, $values)
 * @method CModel_Query<TRelatedModel>|static   orWhereNull($column)
 * @method CModel_Query<TRelatedModel>|static   orWhereNotNull($column)
 * @method CModel_Query<TRelatedModel>|static   whereDate($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>|static   whereDay($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>|static   whereMonth($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>|static   whereYear($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>|static   join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method CModel_Query<TRelatedModel>|static   select($columns = ['*'])
 * @method CModel_Query<TRelatedModel>|static   groupBy(...$groups)
 * @method CModel_Query<TRelatedModel>|static   newQuery()
 * @method CModel_Query<TRelatedModel>|static   withTrashed()
 * @method CModel_Query<TRelatedModel>|static   from($table)
 * @method CModel_Query<TRelatedModel>|static   leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method CModel_Query<TRelatedModel>|static   addSelect($column)
 * @method CModel_Query<TRelatedModel>|static   selectRaw($expression, array $bindings = [])
 * @method CModel_Query<TRelatedModel>|static   orderBy($column, $direction = 'asc')
 * @method CModel_Query<TRelatedModel>|static   orderByDesc($column)
 * @method CModel_Query<TRelatedModel>|static   skip($value)
 * @method CModel_Query<TRelatedModel>|static   offset($value)
 * @method CModel_Query<TRelatedModel>|static   take($value)
 * @method CModel_Query<TRelatedModel>|static   limit($value)
 * @method CModel_Query<TRelatedModel>|static   lockForUpdate()                                                                                                               Lock the selected rows in the table for updating.
 * @method bool                                 exists()                                                                                                                      Determine if any rows exist for the current query
 * @method mixed                                sum($column)                                                                                                                  Retrieve the sum of the values of a given column..
 * @method CModel_Collection<int,TRelatedModel> get($columns = ['*'])
 *
 * @see CModel_Query
 */
abstract class CModel_Relation {
    use CTrait_ForwardsCalls, CTrait_Macroable {
        __call as macroCall;
    }

    /**
     * An array to map class names to their morph names in database.
     *
     * @var array
     */
    public static $morphMap = [];

    /**
     * Prevents morph relationships without a morph map.
     *
     * @var bool
     */
    protected static $requireMorphMap = false;

    /**
     * The model query builder instance.
     *
     * @var CModel_Query
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var CModel
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var CModel
     */
    protected $related;

    /**
     * Indicates whether the eagerly loaded relation should implicitly return an empty collection.
     *
     * @var bool
     */
    protected $eagerKeysWereEmpty = false;

    /**
     * Indicates if the relation is adding constraints.
     *
     * @var bool
     */
    protected static $constraints = true;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Create a new relation instance.
     *
     * @param CModel_Query $query
     * @param CModel       $parent
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $parent) {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }

    /**
     * Run a callback with constraints disabled on the relation.
     *
     * @param \Closure $callback
     *
     * @return mixed
     */
    public static function noConstraints(Closure $callback) {
        $previous = static::$constraints;

        static::$constraints = false;

        // When resetting the relation where clause, we want to shift the first element
        // off of the bindings, leaving only the constraints that the developers put
        // as "extra" on the relationships, and not original relation constraints.
        try {
            return call_user_func($callback);
        } finally {
            static::$constraints = $previous;
        }
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    abstract public function addConstraints();

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    abstract public function addEagerConstraints(array $models);

    /**
     * Initialize the relation on a set of models.
     *
     * @param array  $models
     * @param string $relation
     *
     * @return array
     */
    abstract public function initRelation(array $models, $relation);

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     *
     * @return array
     */
    abstract public function match(array $models, CModel_Collection $results, $relation);

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    abstract public function getResults();

    /**
     * Get the relationship for eager loading.
     *
     * @return CModel_Collection
     */
    public function getEager() {
        return $this->eagerKeysWereEmpty
        ? $this->query->getModel()->newCollection()
        : $this->get();
    }

    /**
     * Execute the query and get the first result if it's the sole matching record.
     *
     * @param array|string $columns
     *
     * @throws \CModel_Exception_ModelNotFoundException<\CModel>
     * @throws \CDatabase_Exception_MultipleRecordsFoundException
     *
     * @return \CModel
     */
    public function sole($columns = ['*']) {
        $result = $this->take(2)->get($columns);

        $count = $result->count();

        if ($count === 0) {
            throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($this->related));
        }

        if ($count > 1) {
            throw new CDatabase_Exception_MultipleRecordsFoundException($count);
        }

        return $result->first();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return CModel_Collection
     */
    public function get($columns = ['*']) {
        return $this->query->get($columns);
    }

    /**
     * Touch all of the related models for the relationship.
     *
     * @return void
     */
    public function touch() {
        $model = $this->getRelated();

        if (!$model::isIgnoringTouch()) {
            $this->rawUpdate([
                $model->getUpdatedAtColumn() => $model->freshTimestampString(),
            ]);
        }
    }

    /**
     * Run a raw update against the base query.
     *
     * @param array $attributes
     *
     * @return int
     */
    public function rawUpdate(array $attributes = []) {
        return $this->query->withoutGlobalScopes()->update($attributes);
    }

    /**
     * Add the constraints for a relationship count query.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     *
     * @return CModel_Query
     */
    public function getRelationExistenceCountQuery(CModel_Query $query, CModel_Query $parentQuery) {
        return $this->getRelationExistenceQuery(
            $query,
            $parentQuery,
            new CDatabase_Query_Expression('count(*)')
        )->setBindings([], 'select');
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $this->getExistenceCompareKey()
        );
    }

    /**
     * Get a relationship join table hash.
     *
     * @param bool $incrementJoinCount
     *
     * @return string
     */
    public function getRelationCountHash($incrementJoinCount = true) {
        return 'laravel_reserved_' . ($incrementJoinCount ? static::$selfJoinCount++ : static::$selfJoinCount);
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param array  $models
     * @param string $key
     *
     * @return array
     */
    protected function getKeys(array $models, $key = null) {
        return c::collect($models)->map(function ($value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        })->values()->unique(null, true)->sort()->all();
    }

    /**
     * Get the query builder that will contain the relationship constraints.
     *
     * @return \CModel_Query
     */
    protected function getRelationQuery() {
        return $this->query;
    }

    /**
     * Get the underlying query for the relation.
     *
     * @return CModel_Query
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Get the base query builder driving the Eloquent builder.
     *
     * @return CDatabase_Query_Builder
     */
    public function getBaseQuery() {
        return $this->query->getQuery();
    }

    /**
     * Get a base query builder instance.
     *
     * @return \CDatabase_Query_Builder
     */
    public function toBase() {
        return $this->query->toBase();
    }

    /**
     * Get the parent model of the relation.
     *
     * @return CModel
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName() {
        return $this->parent->getQualifiedKeyName();
    }

    /**
     * Get the related model of the relation.
     *
     * @return \CModel
     */
    public function getRelated() {
        return $this->related;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function createdAt() {
        return $this->parent->getCreatedAtColumn();
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function updatedAt() {
        return $this->parent->getUpdatedAtColumn();
    }

    /**
     * Get the name of the related model's "updated at" column.
     *
     * @return string
     */
    public function relatedUpdatedAt() {
        return $this->related->getUpdatedAtColumn();
    }

    /**
     * Add a whereIn eager constraint for the given set of model keys to be loaded.
     *
     * @param string        $whereIn
     * @param string        $key
     * @param array         $modelKeys
     * @param \CModel_Query $query
     *
     * @return void
     */
    protected function whereInEager(string $whereIn, string $key, array $modelKeys, $query = null) {
        ($query ?? $this->query)->{$whereIn}($key, $modelKeys);

        if ($modelKeys === []) {
            $this->eagerKeysWereEmpty = true;
        }
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param \CModel $model
     * @param string  $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return $model->getKeyName() === c::last(explode('.', $key))
                    && in_array($model->getKeyType(), ['int', 'integer'])
                        ? 'whereIntegerInRaw'
                        : 'whereIn';
    }

    /**
     * Prevent polymorphic relationships from being used without model mappings.
     *
     * @param bool $requireMorphMap
     *
     * @return void
     */
    public static function requireMorphMap($requireMorphMap = true) {
        static::$requireMorphMap = $requireMorphMap;
    }

    /**
     * Determine if polymorphic relationships require explicit model mapping.
     *
     * @return bool
     */
    public static function requiresMorphMap() {
        return static::$requireMorphMap;
    }

    /**
     * Define the morph map for polymorphic relations and require all morphed models to be explicitly mapped.
     *
     * @param array $map
     * @param bool  $merge
     *
     * @return array
     */
    public static function enforceMorphMap(array $map, $merge = true) {
        static::requireMorphMap();

        return static::morphMap($map, $merge);
    }

    /**
     * Set or get the morph map for polymorphic relations.
     *
     * @param null|array $map
     * @param bool       $merge
     *
     * @return array
     */
    public static function morphMap(array $map = null, $merge = true) {
        $map = static::buildMorphMapFromModels($map);

        if (is_array($map)) {
            static::$morphMap = $merge && static::$morphMap ? $map + static::$morphMap : $map;
        }

        return static::$morphMap;
    }

    /**
     * Builds a table-keyed array from model class names.
     *
     * @param null|string[] $models
     *
     * @return null|array
     */
    protected static function buildMorphMapFromModels(array $models = null) {
        if (is_null($models) || carr::isAssoc($models)) {
            return $models;
        }

        return array_combine(array_map(function ($model) {
            return (new $model())->getTable();
        }, $models), $models);
    }

    /**
     * Get the model associated with a custom polymorphic type.
     *
     * @param string $alias
     *
     * @return null|string
     */
    public static function getMorphedModel($alias) {
        return array_key_exists($alias, self::$morphMap) ? self::$morphMap[$alias] : null;
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $result = $this->forwardCallTo($this->query, $method, $parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone() {
        $this->query = clone $this->query;
    }
}
