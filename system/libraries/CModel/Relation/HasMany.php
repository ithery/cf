<?php

/**
 * Class CModel_Relation_HasMany.
 *
 * @method static CModel|CModel_Collection|static|null        find($id, $columns = ['*'])                                                              Find a model by its primary key.
 * @method static CModel_Collection                           findMany($ids, $columns = ['*'])                                                         Find a model by its primary key.
 * @method static CModel|CModel_Collection|static             findOrFail($id, $columns = ['*'])                                                        Find a model by its primary key or throw an exception.
 * @method static static|CModel|CModel_Query|null             first($columns = ['*'])                                                                  Execute the query and get the first result.
 * @method static CModel|CModel_Query|static                  firstOrFail($columns = ['*'])                                                            Execute the query and get the first result or throw an exception.
 * @method static CModel_Collection|CModel_Query[]|static[]   get($columns = ['*'])                                                                    Execute the query as a "select" statement.
 * @method        mixed                                       value($column)                                                                           Get a single column's value from the first result of a query.
 * @method        mixed                                       pluck($column)                                                                           Get a single column's value from the first result of a query.
 * @method        void                                        chunk($count, callable $callback)                                                        Chunk the results of the query.
 * @method        \CCollection                                lists($column, $key = null)                                                              Get an array with the values of a given column.
 * @method        void                                        onDelete(Closure $callback)                                                              Register a replacement for the default delete function.
 * @method        CModel[]                                    getModels($columns = ['*'])                                                              Get the hydrated models without eager loading.
 * @method        array                                       eagerLoadRelations(array $models)                                                        Eager load the relationships for the models.
 * @method        array                                       loadRelation(array $models, $name, Closure $constraints)                                 Eagerly load the relationship on a set of models.
 * @method static CModel_Query|static                         where($column, $operator = null, $value = null, $boolean = 'and')                        Add a basic where clause to the query.
 * @method static CModel_Query|static                         whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)              Add a relationship count / exists condition to the query with where clauses.
 * @method static CModel_Query|static                         orWhere($column, $operator = null, $value = null)                                        Add an "or where" clause to the query.
 * @method static CModel_Query|static                         has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) Add a relationship count condition to the query.
 * @method static CDatabase_Query_Builder|static              whereRaw($sql, array $bindings = [])
 * @method static CDatabase_Query_Builder|CModel_Query|static whereBetween($column, array $values)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNotBetween($column, array $values)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNested(Closure $callback)
 * @method static CDatabase_Query_Builder|CModel_Query|static addNestedWhereQuery($query)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereExists(Closure $callback)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNotExists(Closure $callback)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereIn($column, $values)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNotIn($column, $values)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNull($column)
 * @method static CDatabase_Query_Builder|CModel_Query|static whereNotNull($column)
 * @method        CModel_Query|static                         orWhereRaw($sql, array $bindings = [])
 * @method        CModel_Query|static                         orWhereBetween($column, array $values)
 * @method        CModel_Query|static                         orWhereNotBetween($column, array $values)
 * @method        CModel_Query|static                         orWhereExists(Closure $callback)
 * @method        CModel_Query|static                         orWhereNotExists(Closure $callback)
 * @method        CModel_Query|static                         orWhereIn($column, $values)
 * @method        CModel_Query|static                         orWhereNotIn($column, $values)
 * @method        CModel_Query|static                         orWhereNull($column)
 * @method        CModel_Query|static                         orWhereNotNull($column)
 * @method        CModel_Query|static                         whereDate($column, $operator, $value)
 * @method        CModel_Query|static                         whereDay($column, $operator, $value)
 * @method        CModel_Query|static                         whereMonth($column, $operator, $value)
 * @method        CModel_Query|static                         whereYear($column, $operator, $value)
 * @method        CModel_Query|static                         join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method        CModel_Query|static                         select($columns = ['*'])
 * @method        CModel_Query|static                         groupBy(...$groups)
 * @method        CModel_Query|static                         newQuery()
 * @method        CModel_Query|static                         withTrashed()
 * @method        CModel_Query|static                         from($table)
 * @method        CModel_Query|static                         leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method        CModel_Query|static                         addSelect($column)
 * @method        CModel_Query|static                         selectRaw($expression, array $bindings = [])
 * @method        CModel_Query|static                         orderBy($column, $direction = 'asc')
 * @method        CModel_Query|static                         skip($value)
 * @method        CModel_Query|static                         offset($value)
 * @method        CModel_Query|static                         take($value)
 * @method        CModel_Query|static                         limit($value)
 * @method        CModel_Query|static                         lockForUpdate()                                                                          Lock the selected rows in the table for updating.
 * @method        bool                                        exists()                                                                                 Determine if any rows exist for the current query.                                                                                                     Lock the selected rows in the table for updating.
 *
 * @see CModel
 * @see CModel_Query
 * @see CDatabase_Query_Builder
 */
class CModel_Relation_HasMany extends CModel_Relation_HasOneOrMany {
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return !is_null($this->getParentKey())
            ? $this->query->get()
            : $this->related->newCollection();
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
        return $this->matchMany($models, $results, $relation);
    }
}
