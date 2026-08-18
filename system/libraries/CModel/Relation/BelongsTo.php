<?php

/**
 * @template TRelatedModel of \CModel
 * @template TDeclaringModel of \CModel
 *
 * @extends CModel_Relation<TRelatedModel, TDeclaringModel, ?TRelatedModel>
 *
 * @method mixed               value($column)                                                                                                          Get a single column's value from the first result of a query.
 * @method mixed               pluck($column)                                                                                                          Get a single column's value from the first result of a query.
 * @method void                chunk($count, callable $callback)                                                                                       Chunk the results of the query.
 * @method \CCollection        lists($column, $key = null)                                                                                             Get an array with the values of a given column.
 * @method void                onDelete(Closure $callback)                                                                                             Register a replacement for the default delete function.
 * @method CModel[]            getModels($columns = ['*'])                                                                                             Get the hydrated models without eager loading.
 * @method array               eagerLoadRelations(array $models)                                                                                       Eager load the relationships for the models.
 * @method array               loadRelation(array $models, $name, Closure $constraints)                                                                Eagerly load the relationship on a set of models.
 * @method static              static                         where($column, $operator = null, $value = null, $boolean = 'and')                        Add a basic where clause to the query.
 * @method static              static                         whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)              Add a relationship count / exists condition to the query with where clauses.
 * @method static              static                         orWhere($column, $operator = null, $value = null)                                        Add an "or where" clause to the query.
 * @method static              static                         has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) Add a relationship count condition to the query.
 * @method static              static                         whereRaw($sql, array $bindings = [])
 * @method static              CDatabase_Query_Builder        whereBetween($column, array $values)
 * @method static              CDatabase_Query_Builder        whereNotBetween($column, array $values)
 * @method static              CDatabase_Query_Builder        whereNested(Closure $callback)
 * @method static              CDatabase_Query_Builder        addNestedWhereQuery($query)
 * @method static              CDatabase_Query_Builder        whereExists(Closure $callback)
 * @method static              CDatabase_Query_Builder        whereNotExists(Closure $callback)
 * @method static              CDatabase_Query_Builder        whereIn($column, $values)
 * @method static              CDatabase_Query_Builder        whereNotIn($column, $values)
 * @method static              static                         whereNull($column)
 * @method static              static                         whereNotNull($column)
 * @method CModel_Query|static orWhereRaw($sql, array $bindings = [])
 * @method CModel_Query|static orWhereBetween($column, array $values)
 * @method CModel_Query|static orWhereNotBetween($column, array $values)
 * @method CModel_Query|static orWhereExists(Closure $callback)
 * @method CModel_Query|static orWhereNotExists(Closure $callback)
 * @method CModel_Query|static orWhereIn($column, $values)
 * @method CModel_Query|static orWhereNotIn($column, $values)
 * @method CModel_Query|static orWhereNull($column)
 * @method CModel_Query|static orWhereNotNull($column)
 * @method CModel_Query|static whereDate($column, $operator, $value)
 * @method CModel_Query|static whereDay($column, $operator, $value)
 * @method CModel_Query|static whereMonth($column, $operator, $value)
 * @method CModel_Query|static whereYear($column, $operator, $value)
 * @method CModel_Query|static join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method CModel_Query|static select($columns = ['*'])
 * @method CModel_Query|static groupBy(...$groups)
 * @method CModel_Query|static newQuery()
 * @method static              withTrashed()
 * @method CModel_Query|static from($table)
 * @method CModel_Query|static leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method CModel_Query|static addSelect($column)
 * @method CModel_Query|static selectRaw($expression, array $bindings = [])
 * @method CModel_Query|static orderBy($column, $direction = 'asc')
 * @method CModel_Query|static orderByDesc($column)
 * @method CModel_Query|static skip($value)
 * @method CModel_Query|static offset($value)
 * @method CModel_Query|static take($value)
 * @method CModel_Query|static limit($value)
 * @method CModel_Query|static lockForUpdate()                                                                                                         Lock the selected rows in the table for updating.
 * @method bool                exists()                                                                                                                Determine if any rows exist for the current query
 * @method mixed               sum($column)                                                                                                            Retrieve the sum of the values of a given column..
 *
 * @see CModel_Query
 */
class CModel_Relation_BelongsTo extends CModel_Relation {
    use CModel_Relation_Trait_ComparesRelatedModels;
    use CModel_Relation_Trait_SupportsDefaultModels;
    use CModel_Relation_Trait_InteractsWithDictionary;

    /**
     * The child model instance of the relation.
     *
     * @var CModel
     */
    protected $child;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The associated key on the parent model.
     *
     * @var string
     */
    protected $ownerKey;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param CModel_Query $query
     * @param CModel       $child
     * @param string       $foreignKey
     * @param string       $ownerKey
     * @param string       $relationName
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $child, $foreignKey, $ownerKey, $relationName) {
        $this->ownerKey = $ownerKey;
        $this->relationName = $relationName;
        $this->foreignKey = $foreignKey;

        // In the underlying base relationship class, this variable is referred to as
        // the "parent" since most relationships are not inversed. But, since this
        // one is we will create a "child" variable for much better readability.
        $this->child = $child;

        parent::__construct($query, $child);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        if (is_null($this->child->{$this->foreignKey})) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $table = $this->related->getTable();

            $this->query->where($table . '.' . $this->ownerKey, '=', $this->child->{$this->foreignKey});
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.
        $key = $this->related->getTable() . '.' . $this->ownerKey;

        $whereIn = $this->whereInMethod($this->related, $this->ownerKey);

        $this->query->{$whereIn}($key, $this->getEagerModelKeys($models));
    }

    /**
     * Gather the keys from an array of related models.
     *
     * @param array $models
     *
     * @return array
     */
    protected function getEagerModelKeys(array $models) {
        $keys = [];

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            if (!is_null($value = $model->{$this->foreignKey})) {
                $keys[] = $value;
            }
        }

        sort($keys);

        return array_values(array_unique($keys));
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
            $model->setRelation($relation, $this->getDefaultFor($model));
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
        $foreign = $this->foreignKey;

        $owner = $this->ownerKey;

        // First we will get to build a dictionary of the child models by their primary
        // key of the relationship, then we can easily match the children back onto
        // the parents using that dictionary and the primary key of the children.
        $dictionary = [];

        foreach ($results as $result) {
            $attribute = $this->getDictionaryKey($result->getAttribute($owner));
            $dictionary[$attribute] = $result;
        }

        // Once we have the dictionary constructed, we can loop through all the parents
        // and match back onto their children using these keys of the dictionary and
        // the primary key of the children to map them onto the correct instances.
        foreach ($models as $model) {
            $attribute = $this->getDictionaryKey($model->{$foreign});
            if (isset($dictionary[$attribute])) {
                $model->setRelation($relation, $dictionary[$attribute]);
            }
        }

        return $models;
    }

    /**
     * Update the parent model on the relationship.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function update(array $attributes) {
        return $this->getResults()->fill($attributes)->save();
    }

    /**
     * Associate the model instance to the given parent.
     *
     * @param \CModel|int|string $model
     *
     * @return \CModel
     */
    public function associate($model) {
        $ownerKey = $model instanceof CModel ? $model->getAttribute($this->ownerKey) : $model;

        $this->child->setAttribute($this->foreignKey, $ownerKey);

        if ($model instanceof CModel) {
            $this->child->setRelation($this->relationName, $model);
        } else {
            $this->child->unsetRelation($this->relationName);
        }

        return $this->child;
    }

    /**
     * Dissociate previously associated model from the given parent.
     *
     * @return \CModel
     */
    public function dissociate() {
        $this->child->setAttribute($this->foreignKey, null);

        return $this->child->setRelation($this->relationName, null);
    }

    /**
     * Alias of "dissociate" method.
     *
     * @return CModel
     */
    public function disassociate() {
        return $this->dissociate();
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

        return $query->select($columns)->whereColumn(
            $this->getQualifiedForeignKeyName(),
            '=',
            $query->qualifyColumn($this->ownerKey)
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
        $query->select($columns)->from(
            $query->getModel()->getTable() . ' as ' . $hash = $this->getRelationCountHash()
        );

        $query->getModel()->setTable($hash);

        return $query->whereColumn(
            $hash . '.' . $this->ownerKey,
            '=',
            $this->getQualifiedForeignKeyName()
        );
    }

    /**
     * Determine if the related model has an auto-incrementing ID.
     *
     * @return bool
     */
    protected function relationHasIncrementingId() {
        return $this->related->getIncrementing()
            && in_array($this->related->getKeyType(), ['int', 'integer']);
    }

    /**
     * Make a new related instance for the given model.
     *
     * @param CModel $parent
     *
     * @return CModel
     */
    protected function newRelatedInstanceFor(CModel $parent) {
        return $this->related->newInstance();
    }

    /**
     * Get the child of the relationship.
     *
     * @return CModel
     */
    public function getChild() {
        return $this->child;
    }

    /**
     * Get the foreign key of the relationship.
     *
     * @return string
     */
    public function getForeignKeyName() {
        return $this->foreignKey;
    }

    /**
     * Get the fully qualified foreign key of the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKeyName() {
        return $this->child->qualifyColumn($this->foreignKey);
    }

    /**
     * Get the key value of the child's foreign key.
     *
     * @return mixed
     */
    public function getParentKey() {
        return $this->child->{$this->foreignKey};
    }

    /**
     * Get the associated key of the relationship.
     *
     * @return string
     */
    public function getOwnerKeyName() {
        return $this->ownerKey;
    }

    /**
     * Get the fully qualified associated key of the relationship.
     *
     * @return string
     */
    public function getQualifiedOwnerKeyName() {
        return $this->related->qualifyColumn($this->ownerKey);
    }

    /**
     * Get the value of the model's associated key.
     *
     * @param CModel $model
     *
     * @return mixed
     */
    protected function getRelatedKeyFrom(CModel $model) {
        return $model->{$this->ownerKey};
    }

    /**
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationName() {
        return $this->relationName;
    }
}
