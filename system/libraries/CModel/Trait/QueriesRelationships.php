<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Dec 25, 2017, 10:08:50 PM
 */
trait CModel_Trait_QueriesRelationships {
    /**
     * Add a relationship count / exists condition to the query.
     *
     * @param string        $relation
     * @param string        $operator
     * @param int           $count
     * @param string        $boolean
     * @param null|\Closure $callback
     *
     * @return CModel_Query|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) {
        if (is_string($relation)) {
            if (strpos($relation, '.') !== false) {
                return $this->hasNested($relation, $operator, $count, $boolean, $callback);
            }
            $relation = $this->getRelationWithoutConstraints($relation);
        }

        if ($relation instanceof CModel_Relation_MorphTo) {
            return $this->hasMorph($relation, ['*'], $operator, $count, $boolean, $callback);
        }

        // If we only need to check for the existence of the relation, then we can optimize
        // the subquery to only run a "where exists" clause instead of this full "count"
        // clause. This will make these queries run much faster compared with a count.
        $method = $this->canUseExistsForExistenceCheck($operator, $count)
            ? 'getRelationExistenceQuery'
            : 'getRelationExistenceCountQuery';

        $hasQuery = $relation->{$method}(
            $relation->getRelated()->newQueryWithoutRelationships(),
            $this
        );

        // Next we will call any given callback as an "anonymous" scope so they can get the
        // proper logical grouping of the where clauses if needed by this Eloquent query
        // builder. Then, we will be ready to finalize and return this query instance.
        if ($callback) {
            $hasQuery->callScope($callback);
        }

        return $this->addHasWhere(
            $hasQuery,
            $relation,
            $operator,
            $count,
            $boolean
        );
    }

    /**
     * Add nested relationship count / exists conditions to the query.
     *
     * Sets up recursive call to whereHas until we finish the nested relation.
     *
     * @param string        $relations
     * @param string        $operator
     * @param int           $count
     * @param string        $boolean
     * @param null|\Closure $callback
     *
     * @return CModel_Query|static
     */
    protected function hasNested($relations, $operator = '>=', $count = 1, $boolean = 'and', $callback = null) {
        $relations = explode('.', $relations);

        $doesntHave = $operator === '<' && $count === 1;

        if ($doesntHave) {
            $operator = '>=';
            $count = 1;
        }
        $closure = function ($q) use (&$closure, &$relations, $operator, $count, $callback) {
            // In order to nest "has", we need to add count relation constraints on the
            // callback Closure. We'll do this by simply passing the Closure its own
            // reference to itself so it calls itself recursively on each segment.
            count($relations) > 1
                ? $q->whereHas(array_shift($relations), $closure)
                : $q->has(array_shift($relations), $operator, $count, 'and', $callback);
        };

        return $this->has(array_shift($relations), $doesntHave ? '<' : '>=', 1, $boolean, $closure);
    }

    /**
     * Add a relationship count / exists condition to the query with an "or".
     *
     * @param string $relation
     * @param string $operator
     * @param int    $count
     *
     * @return CModel_Query|static
     */
    public function orHas($relation, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'or');
    }

    /**
     * Add a relationship count / exists condition to the query.
     *
     * @param string        $relation
     * @param string        $boolean
     * @param null|\Closure $callback
     *
     * @return CModel_Query|static
     */
    public function doesntHave($relation, $boolean = 'and', Closure $callback = null) {
        return $this->has($relation, '<', 1, $boolean, $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with an "or".
     *
     * @param string $relation
     *
     * @return CModel_Query|static
     */
    public function orDoesntHave($relation) {
        return $this->doesntHave($relation, 'or');
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
     *
     * @param string        $relation
     * @param null|\Closure $callback
     * @param string        $operator
     * @param int           $count
     *
     * @return CModel_Query|static
     */
    public function whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'and', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
     *
     * Also load the relationship with same condition.
     *
     * @param  \CModel_Relation<*, *, *>|string  $relation
     * @param null|\Closure $callback
     * @param string        $operator
     * @param int           $count
     *
     * @return $this
     */
    public function withWhereHas($relation, Closure $callback = null, $operator = '>=', $count = 1) {
        return $this->whereHas(cstr::before($relation, ':'), $callback, $operator, $count)
            ->with($callback ? [$relation => fn ($query) => $callback($query)] : $relation);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses and an "or".
     *
     * @param \CModel_Relation<*, *, *>|string   $relation
     * @param null|\Closure $callback
     * @param string        $operator
     * @param int           $count
     *
     * @return CModel_Query|static
     */
    public function orWhereHas($relation, Closure $callback = null, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'or', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
     *
     * @param \CModel_Relation<*, *, *>|string   $relation
     * @param null|\Closure $callback
     *
     * @return CModel_Query|static
     */
    public function whereDoesntHave($relation, Closure $callback = null) {
        return $this->doesntHave($relation, 'and', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses and an "or".
     *
     * @param \CModel_Relation<*, *, *>|string   $relation
     * @param null|\Closure $callback
     *
     * @return CModel_Query|static
     */
    public function orWhereDoesntHave($relation, Closure $callback = null) {
        return $this->doesntHave($relation, 'or', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query.
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param string                          $operator
     * @param int                             $count
     * @param string                          $boolean
     * @param null|\Closure                   $callback
     *
     * @return \CModel_Query|static
     */
    public function hasMorph($relation, $types, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) {
        if (is_string($relation)) {
            $relation = $this->getRelationWithoutConstraints($relation);
        }

        $types = (array) $types;

        if ($types === ['*']) {
            $types = $this->model->newModelQuery()->distinct()->pluck($relation->getMorphType())->filter()->all();
        }

        if (empty($types)) {
            return $this->where(new CDatabase_Query_Expression('0'), $operator, $count, $boolean);
        }

        foreach ($types as &$type) {
            $type = CModel_Relation::getMorphedModel($type) ?: $type;
        }

        return $this->where(function ($query) use ($relation, $callback, $operator, $count, $types) {
            foreach ($types as $type) {
                $query->orWhere(function ($query) use ($relation, $callback, $operator, $count, $type) {
                    $belongsTo = $this->getBelongsToRelation($relation, $type);

                    if ($callback) {
                        $callback = function ($query) use ($callback, $type) {
                            return $callback($query, $type);
                        };
                    }

                    $query->where($this->qualifyColumn($relation->getMorphType()), '=', (new $type())->getMorphClass())
                        ->whereHas($belongsTo, $callback, $operator, $count);
                });
            }
        }, null, null, $boolean);
    }

    /**
     * Get the BelongsTo relationship for a single polymorphic type.
     *
     * @template TRelatedModel of \CModel
     * @template TDeclaringModel of \CModel
     *
     * @param \CModel_Relation_MorphTo<*, TDeclaringModel> $relation
     * @param class-string<TRelatedModel> $type
     *
     * @return \CModel_Relation_BelongsTo<TRelatedModel, TDeclaringModel>
     */
    protected function getBelongsToRelation(CModel_Relation_MorphTo $relation, $type) {
        $belongsTo = CModel_Relation_BelongsTo::noConstraints(function () use ($relation, $type) {
            return $this->model->belongsTo(
                $type,
                $relation->getForeignKeyName(),
                $relation->getOwnerKeyName()
            );
        });

        $belongsTo->getQuery()->mergeConstraintsFrom($relation->getQuery());

        return $belongsTo;
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with an "or".
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param string                          $operator
     * @param int                             $count
     *
     * @return \CModel_Query|static
     */
    public function orHasMorph($relation, $types, $operator = '>=', $count = 1) {
        return $this->hasMorph($relation, $types, $operator, $count, 'or');
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query.
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param string                          $boolean
     * @param null|\Closure                   $callback
     *
     * @return \CModel_Query|static
     */
    public function doesntHaveMorph($relation, $types, $boolean = 'and', Closure $callback = null) {
        return $this->hasMorph($relation, $types, '<', 1, $boolean, $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with an "or".
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     *
     * @return \CModel_Query|static
     */
    public function orDoesntHaveMorph($relation, $types) {
        return $this->doesntHaveMorph($relation, $types, 'or');
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses.
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param null|\Closure                   $callback
     * @param string                          $operator
     * @param int                             $count
     *
     * @return \CModel_Query|static
     */
    public function whereHasMorph($relation, $types, Closure $callback = null, $operator = '>=', $count = 1) {
        return $this->hasMorph($relation, $types, $operator, $count, 'and', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param null|\Closure                   $callback
     * @param string                          $operator
     * @param int                             $count
     *
     * @return \CModel_Query|static
     */
    public function orWhereHasMorph($relation, $types, Closure $callback = null, $operator = '>=', $count = 1) {
        return $this->hasMorph($relation, $types, $operator, $count, 'or', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses.
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param null|\Closure                   $callback
     *
     * @return \CModel_Query|static
     */
    public function whereDoesntHaveMorph($relation, $types, Closure $callback = null) {
        return $this->doesntHaveMorph($relation, $types, 'and', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
     *
     * @param \CModel_Relation_MorphTo|string $relation
     * @param string|array                    $types
     * @param null|\Closure                   $callback
     *
     * @return \CModel_Query|static
     */
    public function orWhereDoesntHaveMorph($relation, $types, Closure $callback = null) {
        return $this->doesntHaveMorph($relation, $types, 'or', $callback);
    }

    /**
     * Add subselect queries to include an aggregate value for a relationship.
     *
     * @param mixed  $relations
     * @param string $column
     * @param string $function
     *
     * @return $this
     */
    public function withAggregate($relations, $column, $function = null) {
        if (empty($relations)) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from . '.*']);
        }

        $relations = is_array($relations) ? $relations : [$relations];

        foreach ($this->parseWithRelations($relations) as $name => $constraints) {
            // First we will determine if the name has been aliased using an "as" clause on the name
            // and if it has we will extract the actual relationship name and the desired name of
            // the resulting column. This allows multiple aggregates on the same relationships.
            $segments = explode(' ', $name);

            unset($alias);

            if (count($segments) === 3 && cstr::lower($segments[1]) === 'as') {
                list($name, $alias) = [$segments[0], $segments[2]];
            }

            $relation = $this->getRelationWithoutConstraints($name);

            if ($function) {
                if ($column instanceof CDatabase_Query_Expression) {
                    $wrappedColumn = $column->getValue($this->getQuery()->getGrammar());
                } else {
                    $hashedColumn = $this->getRelationHashedColumn($column, $relation);

                    $wrappedColumn = $this->getQuery()->getGrammar()->wrap(
                        $column === '*' ? $column : $relation->getRelated()->qualifyColumn($hashedColumn)
                    );
                }
                $expression = $function === 'exists' ? $wrappedColumn : sprintf('%s(%s)', $function, $wrappedColumn);
            } else {
                $expression = $column;
            }

            // Here, we will grab the relationship sub-query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // sub-query. We'll format this relationship name and append this column if needed.
            $query = $relation->getRelationExistenceQuery(
                $relation->getRelated()->newQuery(),
                $this,
                new CDatabase_Query_Expression($expression)
            )->setBindings([], 'select');

            $query->callScope($constraints);

            $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

            // If the query contains certain elements like orderings / more than one column selected
            // then we will remove those elements from the query so that it will execute properly
            // when given to the database. Otherwise, we may receive SQL errors or poor syntax.
            $query->orders = null;
            $query->setBindings([], 'order');

            if (count($query->columns) > 1) {
                $query->columns = [$query->columns[0]];
                $query->bindings['select'] = [];
            }

            // Finally, we will make the proper column alias to the query and run this sub-select on
            // the query builder. Then, we will return the builder instance back to the developer
            // for further constraint chaining that needs to take place on the query as needed.
            $alias = (isset($alias) && $alias !== null) ? $alias : cstr::snake(
                preg_replace('/[^[:alnum:][:space:]_]/u', '', "${name} ${function} ${column}")
            );

            if ($function === 'exists') {
                $this->selectRaw(
                    sprintf('exists(%s) as %s', $query->toSql(), $this->getQuery()->grammar->wrap($alias)),
                    $query->getBindings()
                )->withCasts([$alias => 'bool']);
            } else {
                $this->selectSub(
                    $function ? $query : $query->limit(1),
                    $alias
                );
            }
        }

        return $this;
    }

    /**
     * Add a basic where clause to a relationship query.
     *
     * @param string                                            $relation
     * @param \Closure|string|array|\CDatabase_Query_Expression $column
     * @param mixed                                             $operator
     * @param mixed                                             $value
     *
     * @return \CModel_Query|static
     */
    public function whereRelation($relation, $column, $operator = null, $value = null) {
        return $this->whereHas($relation, function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        });
    }

    /**
     * Add an "or where" clause to a relationship query.
     *
     * @param string                                            $relation
     * @param \Closure|string|array|\CDatabase_Query_Expression $column
     * @param mixed                                             $operator
     * @param mixed                                             $value
     *
     * @return \CModel_Query|static
     */
    public function orWhereRelation($relation, $column, $operator = null, $value = null) {
        return $this->orWhereHas($relation, function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        });
    }

    /**
     * Add a polymorphic relationship condition to the query with a where clause.
     *
     * @param \CModel_Relation_MorphTo|string                   $relation
     * @param string|array                                      $types
     * @param \Closure|string|array|\CDatabase_Query_Expression $column
     * @param mixed                                             $operator
     * @param mixed                                             $value
     *
     * @return \CModel_Query|static
     */
    public function whereMorphRelation($relation, $types, $column, $operator = null, $value = null) {
        return $this->whereHasMorph($relation, $types, function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        });
    }

    /**
     * Add a polymorphic relationship condition to the query with an "or where" clause.
     *
     * @param \CModel_Relation_MorphTo|string                   $relation
     * @param string|array                                      $types
     * @param \Closure|string|array|\CDatabase_Query_Expression $column
     * @param mixed                                             $operator
     * @param mixed                                             $value
     *
     * @return \CModel_Query|static
     */
    public function orWhereMorphRelation($relation, $types, $column, $operator = null, $value = null) {
        return $this->orWhereHasMorph($relation, $types, function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        });
    }

    /**
     * Add a morph-to relationship condition to the query.
     *
     * @param \CModel_Relation_MorphTo<*, *>|string $relation
     * @param null|\CModel|string $model
     * @param string              $boolean
     *
     * @return \CModel_Query|static
     */
    public function whereMorphedTo($relation, $model, $boolean = 'and') {
        if (is_string($relation)) {
            $relation = $this->getRelationWithoutConstraints($relation);
        }

        if (is_null($model)) {
            return $this->whereNull($relation->getMorphType(), $boolean);
        }

        if (is_string($model)) {
            $morphMap = CModel_Relation::morphMap();

            if (!empty($morphMap) && in_array($model, $morphMap)) {
                $model = array_search($model, $morphMap, true);
            }

            return $this->where($relation->getMorphType(), $model, null, $boolean);
        }

        return $this->where(function ($query) use ($relation, $model) {
            $query->where($relation->getMorphType(), $model->getMorphClass())
                ->where($relation->getForeignKeyName(), $model->getKey());
        }, null, null, $boolean);
    }

    /**
     * Add a not morph-to relationship condition to the query.
     *
     * @param \CModel_Relation_MorphTo<*, *>|string $relation
     * @param \CModel|string $model
     * @param mixed          $boolean
     *
     * @return $this
     */
    public function whereNotMorphedTo($relation, $model, $boolean = 'and') {
        if (is_string($relation)) {
            $relation = $this->getRelationWithoutConstraints($relation);
        }

        if (is_string($model)) {
            $morphMap = CModel_Relation::morphMap();

            if (!empty($morphMap) && in_array($model, $morphMap)) {
                $model = array_search($model, $morphMap, true);
            }

            return $this->whereNot($relation->getMorphType(), '<=>', $model, $boolean);
        }

        return $this->whereNot(function ($query) use ($relation, $model) {
            $query->where($relation->getMorphType(), '<=>', $model->getMorphClass())
                ->where($relation->getForeignKeyName(), '<=>', $model->getKey());
        }, null, null, $boolean);
    }

    /**
     * Add a morph-to relationship condition to the query with an "or where" clause.
     *
     * @param \CModel_Relation_MorphTo<*, *>|string $relation
     * @param null|\CModel|string $model
     *
     * @return \CModel_Query|static
     */
    public function orWhereMorphedTo($relation, $model) {
        return $this->whereMorphedTo($relation, $model, 'or');
    }

    /**
     * Add a not morph-to relationship condition to the query with an "or where" clause.
     *
     * @param \CModel_Relation_MorphTo<*, *>|string $relation
     * @param null|\CModel|string $model
     *
     * @return \CModel_Query|static
     */
    public function orWhereNotMorphedTo($relation, $model) {
        return $this->whereNotMorphedTo($relation, $model, 'or');
    }

    /**
     * Add a "belongs to" relationship where clause to the query.
     *
     * @param \CModel|\CModel_Collection<int, \CModel> $related
     * @param null|string                              $relationshipName
     * @param string                                   $boolean
     *
     * @throws \CModel_Exception_RelationNotFoundException
     *
     * @return $this
     */
    public function whereBelongsTo($related, $relationshipName = null, $boolean = 'and') {
        if (!$related instanceof CCollection) {
            $relatedCollection = $related->newCollection([$related]);
        } else {
            $relatedCollection = $related;

            $related = $relatedCollection->first();
        }
        if ($relatedCollection->isEmpty()) {
            throw new InvalidArgumentException('Collection given to whereBelongsTo method may not be empty.');
        }
        if ($relationshipName === null) {
            $relationshipName = cstr::camel(c::classBasename($related));
        }

        try {
            $relationship = $this->model->{$relationshipName}();
        } catch (BadMethodCallException $exception) {
            throw CModel_Exception_RelationNotFoundException::make($this->model, $relationshipName);
        }

        if (!$relationship instanceof CModel_Relation_BelongsTo) {
            throw CModel_Exception_RelationNotFoundException::make($this->model, $relationshipName, CModel_Relation_BelongsTo::class);
        }
        $this->whereIn(
            $relationship->getQualifiedForeignKeyName(),
            $relatedCollection->pluck($relationship->getOwnerKeyName())->toArray(),
            $boolean,
        );

        return $this;
    }

    /**
     * Add an "BelongsTo" relationship with an "or where" clause to the query.
     *
     * @param \CModel     $related
     * @param null|string $relationshipName
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function orWhereBelongsTo($related, $relationshipName = null) {
        return $this->whereBelongsTo($related, $relationshipName, 'or');
    }

    /**
     * Get the relation hashed column name for the given column and relation.
     *
     * @param string           $column
     * @param \CModel_Relation $relation
     *
     * @return string
     */
    protected function getRelationHashedColumn($column, $relation) {
        if (cstr::contains($column, '.')) {
            return $column;
        }

        return $this->getQuery()->from === $relation->getQuery()->getQuery()->from
            ? "{$relation->getRelationCountHash(false)}.$column"
            : $column;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount($relations) {
        return $this->withAggregate(is_array($relations) ? $relations : func_get_args(), '*', 'count');
    }

    /**
     * Add subselect queries to include the max of the relation's column.
     *
     * @param string|array $relation
     * @param string       $column
     *
     * @return $this
     */
    public function withMax($relation, $column) {
        return $this->withAggregate($relation, $column, 'max');
    }

    /**
     * Add subselect queries to include the min of the relation's column.
     *
     * @param string|array $relation
     * @param string       $column
     *
     * @return $this
     */
    public function withMin($relation, $column) {
        return $this->withAggregate($relation, $column, 'min');
    }

    /**
     * Add subselect queries to include the sum of the relation's column.
     *
     * @param string|array $relation
     * @param string       $column
     *
     * @return $this
     */
    public function withSum($relation, $column) {
        return $this->withAggregate($relation, $column, 'sum');
    }

    /**
     * Add subselect queries to include the average of the relation's column.
     *
     * @param string|array $relation
     * @param string       $column
     *
     * @return $this
     */
    public function withAvg($relation, $column) {
        return $this->withAggregate($relation, $column, 'avg');
    }

    /**
     * Add the "has" condition where clause to the query.
     *
     * @param CModel_Query    $hasQuery
     * @param CModel_Relation $relation
     * @param string          $operator
     * @param int             $count
     * @param string          $boolean
     *
     * @return CModel_Query|static
     */
    protected function addHasWhere(CModel_Query $hasQuery, CModel_Relation $relation, $operator, $count, $boolean) {
        $hasQuery->mergeConstraintsFrom($relation->getQuery());

        return $this->canUseExistsForExistenceCheck($operator, $count) ? $this->addWhereExistsQuery($hasQuery->toBase(), $boolean, $operator === '<' && $count === 1) : $this->addWhereCountQuery($hasQuery->toBase(), $operator, $count, $boolean);
    }

    /**
     * Merge the where constraints from another query to the current query.
     *
     * @param CModel_Query $from
     *
     * @return CModel_Query|static
     */
    public function mergeConstraintsFrom(CModel_Query $from) {
        $rawBindings = $from->getQuery()->getRawBindings();
        $whereBindings = isset($rawBindings['where']) ? $rawBindings['where'] : [];

        // Here we have some other query that we want to merge the where constraints from. We will
        // copy over any where constraints on the query as well as remove any global scopes the
        // query might have removed. Then we will return ourselves with the finished merging.
        return $this->withoutGlobalScopes(
            $from->removedScopes()
        )->mergeWheres(
            $from->getQuery()->wheres,
            $whereBindings
        );
    }

    /**
     * Add a sub-query count clause to this query.
     *
     * @param CDatabase_Query_Builder $query
     * @param string                  $operator
     * @param int                     $count
     * @param string                  $boolean
     *
     * @return $this
     */
    protected function addWhereCountQuery(CDatabase_Query_Builder $query, $operator = '>=', $count = 1, $boolean = 'and') {
        $this->query->addBinding($query->getBindings(), 'where');

        return $this->where(
            new CDatabase_Query_Expression('(' . $query->toSql() . ')'),
            $operator,
            is_numeric($count) ? new CDatabase_Query_Expression($count) : $count,
            $boolean
        );
    }

    /**
     * Get the "has relation" base query instance.
     *
     * @param string $relation
     *
     * @return CModel_Relation
     */
    protected function getRelationWithoutConstraints($relation) {
        return CModel_Relation::noConstraints(function () use ($relation) {
            return $this->getModel()->{$relation}();
        });
    }

    /**
     * Check if we can run an "exists" query to optimize performance.
     *
     * @param string $operator
     * @param int    $count
     *
     * @return bool
     */
    protected function canUseExistsForExistenceCheck($operator, $count) {
        return ($operator === '>=' || $operator === '<') && $count === 1;
    }
}
