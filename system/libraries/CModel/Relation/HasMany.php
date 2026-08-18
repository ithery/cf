<?php

/**
 * Class CModel_Relation_HasMany.
 *
 * @template TRelatedModel of \CModel
 * @template TDeclaringModel of \CModel
 *
 * @extends \CModel_Relation_HasOneOrMany<TRelatedModel, TDeclaringModel, \CModel_Collection<int, TRelatedModel>>
 *
 * @method null|CModel|CModel_Collection|static                find($id, $columns = ['*'])                                                              Find a model by its primary key.
 * @method CModel_Collection                                   findMany($ids, $columns = ['*'])                                                         Find a model by its primary key.
 * @method CModel|CModel_Collection|static                     findOrFail($id, $columns = ['*'])                                                        Find a model by its primary key or throw an exception.
 * @method null|static|CModel|CModel_Query<TRelatedModel>      first($columns = ['*'])                                                                  Execute the query and get the first result.
 * @method CModel|CModel_Query<TRelatedModel>                  firstOrFail($columns = ['*'])                                                            Execute the query and get the first result or throw an exception.
 * @method CModel_Collection|CModel_Query[]|static[]           get($columns = ['*'])                                                                    Execute the query as a "select" statement.
 * @method mixed                                               value($column)                                                                           Get a single column's value from the first result of a query.
 * @method mixed                                               pluck($column)                                                                           Get a single column's value from the first result of a query.
 * @method void                                                chunk($count, callable $callback)                                                        Chunk the results of the query.
 * @method \CCollection                                        lists($column, $key = null)                                                              Get an array with the values of a given column.
 * @method void                                                onDelete(Closure $callback)                                                              Register a replacement for the default delete function.
 * @method CModel[]                                            getModels($columns = ['*'])                                                              Get the hydrated models without eager loading.
 * @method array                                               eagerLoadRelations(array $models)                                                        Eager load the relationships for the models.
 * @method array                                               loadRelation(array $models, $name, Closure $constraints)                                 Eagerly load the relationship on a set of models.
 * @method CModel_Query<TRelatedModel>                         where($column, $operator = null, $value = null, $boolean = 'and')                        Add a basic where clause to the query.
 * @method CModel_Query<TRelatedModel>                         whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)              Add a relationship count / exists condition to the query with where clauses.
 * @method CModel_Query<TRelatedModel>                         orWhere($column, $operator = null, $value = null)                                        Add an "or where" clause to the query.
 * @method CModel_Query<TRelatedModel>                         has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) Add a relationship count condition to the query.
 * @method CDatabase_Query_Builder                             whereRaw($sql, array $bindings = [])
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereBetween($column, array $values)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNotBetween($column, array $values)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNested(Closure $callback)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> addNestedWhereQuery($query)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereExists(Closure $callback)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNotExists(Closure $callback)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereIn($column, $values)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNotIn($column, $values)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNull($column)
 * @method CDatabase_Query_Builder|CModel_Query<TRelatedModel> whereNotNull($column)
 * @method CModel_Query<TRelatedModel>                         orWhereRaw($sql, array $bindings = [])
 * @method CModel_Query<TRelatedModel>                         orWhereBetween($column, array $values)
 * @method CModel_Query<TRelatedModel>                         orWhereNotBetween($column, array $values)
 * @method CModel_Query<TRelatedModel>                         orWhereExists(Closure $callback)
 * @method CModel_Query<TRelatedModel>                         orWhereNotExists(Closure $callback)
 * @method CModel_Query<TRelatedModel>                         orWhereIn($column, $values)
 * @method CModel_Query<TRelatedModel>                         orWhereNotIn($column, $values)
 * @method CModel_Query<TRelatedModel>                         orWhereNull($column)
 * @method CModel_Query<TRelatedModel>                         orWhereNotNull($column)
 * @method CModel_Query<TRelatedModel>                         whereDate($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>                         whereDay($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>                         whereMonth($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>                         whereYear($column, $operator, $value)
 * @method CModel_Query<TRelatedModel>                         join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method CModel_Query<TRelatedModel>                         select($columns = ['*'])
 * @method CModel_Query<TRelatedModel>                         groupBy(...$groups)
 * @method CModel_Query<TRelatedModel>                         newQuery()
 * @method CModel_Query<TRelatedModel>                         withTrashed()
 * @method CModel_Query<TRelatedModel>                         from($table)
 * @method CModel_Query<TRelatedModel>                         leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method CModel_Query<TRelatedModel>                         addSelect($column)
 * @method CModel_Query<TRelatedModel>                         selectRaw($expression, array $bindings = [])
 * @method CModel_Query<TRelatedModel>                         orderBy($column, $direction = 'asc')
 * @method CModel_Query<TRelatedModel>                         skip($value)
 * @method CModel_Query<TRelatedModel>                         offset($value)
 * @method CModel_Query<TRelatedModel>                         take($value)
 * @method CModel_Query<TRelatedModel>                         limit($value)
 * @method CModel_Query<TRelatedModel>                         lockForUpdate()                                                                          Lock the selected rows in the table for updating.
 * @method bool                                                exists()                                                                                 Determine if any rows exist for the current query.                                                                                                     Lock the selected rows in the table for updating.
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
