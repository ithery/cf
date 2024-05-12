<?php

use Opis\Closure\SerializableClosure;

class CManager_DataProvider_ModelDataProvider extends CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    protected $modelClass;

    /**
     * @var SerializableClosure
     */
    protected $queryCallback;

    public function __construct($modelClass, $queryCallback = null) {
        $this->modelClass = $modelClass;
        $this->queryCallback = $queryCallback != null ? new SerializableClosure($queryCallback) : null;
    }

    /**
     * @param CModel_Query $query
     *
     * @return array
     */
    protected function getAggregateFieldFromQuery(CModel_Query $query) {
        $columns = $query->toBase()->columns;
        $fields = [];
        if ($columns !== null) {
            foreach ($columns as $col) {
                if ($col instanceof CDatabase_Query_Expression) {
                    $statement = $col->getValue($query->getGrammar());
                    //$regex = '/([\w]++)`?+(?:\s++as\s++[^,\s]++)?+\s*+(?:FROM\s*+|$)/i';
                    // $regex = '/([\w]++)`?+\s*+(?:FROM\s*+|$)/i';
                    $regex = '/([\w]++)`?+\s*+$/i';
                    if ($statement instanceof CDatabase_Query_Expression) {
                        $statement = $statement->getValue($query->getGrammar());
                    }
                    if (preg_match($regex, $statement, $match)) {
                        $fields[] = $match[1]; // field stored in $match[1]
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @param mixed $callback
     *
     * @return CModel_Query
     */
    public function getModelQuery($callback = null) {
        $modelClass = $this->modelClass;
        $query = $modelClass::query();
        /** @var CModel_Query $query */
        if ($this->queryCallback) {
            if ($this->queryCallback instanceof SerializableClosure) {
                $this->queryCallback->__invoke($query);
            } else {
                call_user_func_array($this->queryCallback, [$query]);
            }
        }

        if ($callback) {
            if ($callback instanceof SerializableClosure) {
                $callback->__invoke($query);
            } else {
                call_user_func_array($callback, [$query]);
            }
        }

        $aggregateFields = $this->getAggregateFieldFromQuery($query);

        //process search
        if (count($this->searchOr) > 0) {
            $dataSearch = $this->searchOr;
            $query->where(function (CModel_Query $q) use ($dataSearch, $aggregateFields) {
                foreach ($dataSearch as $fieldName => $value) {
                    if ($this->isCallable($value)) {
                        $q->orWhere(function ($q) use ($value) {
                            $this->callCallable($value, [$q]);
                        });
                    } else {
                        if (strpos($fieldName, '.') !== false) {
                            $fields = explode('.', $fieldName);

                            $field = array_pop($fields);
                            $relation = implode('.', $fields);
                            $q->orWhereHas($relation, function ($q2) use ($value, $field) {
                                $table = $q2->getModel()->getTable();

                                $q2->where($table . '.' . $field, 'like', '%' . $value . '%');
                            });
                        } else {
                            //check this is aggregate field where or not
                            if (in_array($fieldName, $aggregateFields)) {
                                //TODO apply search on aggregateFields
                            } else {
                                if (!$this->isRelationField($q, $fieldName)) {
                                    $q->orWhere($fieldName, 'like', '%' . $value . '%');
                                }
                            }
                        }
                    }
                }
            });
        }

        if (count($this->searchAnd) > 0) {
            $dataSearch = $this->searchAnd;
            $query->where(function (CModel_Query $q) use ($dataSearch, $aggregateFields) {
                foreach ($dataSearch as $fieldName => $value) {
                    if ($this->isCallable(carr::get($dataSearch, $fieldName))) {
                        $q->where(function ($q) use ($value) {
                            $this->callCallable($value, [$q]);
                        });
                    } else {
                        if (strpos($fieldName, '.') !== false) {
                            $fields = explode('.', $fieldName);

                            $field = array_pop($fields);
                            $relation = implode('.', $fields);

                            $q->whereHas($relation, function ($q2) use ($value, $field) {
                                $q2->where($field, 'like', '%' . $value . '%');
                            });
                        } else {
                            if (in_array($fieldName, $aggregateFields)) {
                                //TODO apply search on aggregateFields
                            } else {
                                if (!$this->isRelationField($q, $fieldName)) {
                                    $q->where($fieldName, 'like', '%' . $value . '%');
                                }
                            }
                        }
                    }
                }
            });

            if (count($aggregateFields) > 0) {
                foreach ($dataSearch as $fieldName => $value) {
                    if (strpos($fieldName, '.') === false) {
                        if (in_array($fieldName, $aggregateFields)) {
                            $query->having($fieldName, 'like', '%' . $value . '%');
                        }
                    }
                }
            }
        }

        //process ordering
        if (count($this->sort) > 0) {
            $query->getQuery()->orders = null;
            $sortIndex = 0;
            foreach ($this->sort as $fieldName => $sortDirection) {
                if (strpos($fieldName, '.') !== false) {
                    $fields = explode('.', $fieldName);

                    $field = array_pop($fields);
                    $relationPath = implode('.', $fields);

                    $alias = $this->withSelectRelationColumn($query, $relationPath, $field, $sortIndex);
                    if ($alias) {
                        $query->orderBy($alias, $sortDirection);
                    }
                } else {
                    if (!$this->isRelationField($query, $fieldName)) {
                        $query->orderBy($fieldName, $sortDirection);
                    }
                }
                $sortIndex++;
            }
        }

        return $query;
    }

    protected function withSelectRelationColumn($query, $relationPath, $column, $index) {
        $alias = 'mdp_sort_' . $index;

        $relations = explode('.', $relationPath);
        $firstRelation = array_shift($relations);
        if (!method_exists($query->getModel(), $firstRelation)) {
            return null;
        }
        $relation = $query->getModel()->$firstRelation();

        $selectQuery = $this->createSelectJoinQuery($query, $relation, $relations, $column);

        if ($index == 0) {
            $query->addSelect($query->getModel()->getTable() . '.*');
        }

        $query->selectSub($selectQuery, $alias);

        return $alias;
    }

    /**
     * @param $length
     *
     * @return string
     */
    public static function randomStringAlpha($length) {
        $pool = array_merge(range('a', 'z'), range('A', 'Z'));
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }

        return $key;
    }

    protected function createSelectJoinQuery(CModel_Query $query, CModel_Relation $relation, array $joinRelations, $column) {
        $tableAlias = 'mdp_join_main';
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();
        $relatedConnection = $relatedModel->getConnection();
        $newQuery = $relatedConnection->newQuery()
            ->from($relatedTable, $tableAlias);

        $currentModel = $relatedModel;
        $joinIndex = 0;
        $beforeAlias = $tableAlias;
        $columnAlias = $tableAlias;

        foreach ($joinRelations as $joinRelation) {
            $joinAlias = 'mdp_join_' . $joinIndex;
            $currentRelation = $currentModel->$joinRelation();
            $joinModel = $currentRelation->getRelated();
            $joinTable = $joinModel->getTable();

            if ($currentRelation instanceof CModel_Relation_BelongsTo) {
                $newQuery->leftJoin($joinTable . ' AS ' . $joinAlias, $joinAlias . '.' . $joinModel->getKeyName(), '=', $beforeAlias . '.' . $currentRelation->getForeignKeyName());
            } else {
                throw new Exception('Far Relation currently support BelongsTo only');
            }

            $currentModel = $joinModel;
            $joinIndex++;
            $columnAlias = $joinAlias;
        }

        if ($relation instanceof CModel_Relation_BelongsTo) {
            $newQuery->whereColumn(
                $relation->getQualifiedForeignKeyName(),
                '=',
                $tableAlias . '.' . $relation->getOwnerKeyName()
            );
        }
        if ($relation instanceof CModel_Relation_HasOneOrMany) {
            $newQuery->whereColumn(
                $relation->getQualifiedParentKeyName(),
                '=',
                $tableAlias . '.' . $relation->getForeignKeyName()
            )->limit(1);
        }
        if ($relation instanceof CModel_Relation_MorphOneOrMany) {
            $newQuery->where(
                $tableAlias . '.' . $relation->getMorphType(),
                $relation->getMorphClass()
            );
        }
        $newQuery->select($columnAlias . '.' . $column);

        if ($relation instanceof CModel_Relation_BelongsToOne) {
            $joinAlias = 'mdp_bto_join_' . $column;

            $joinTable = $relation->getTable();
            $newQuery->join($joinTable . ' AS ' . $joinAlias, $joinAlias . '.' . $relation->getRelatedPivotKeyName(), '=', $tableAlias . '.' . $relation->getRelatedKeyName());
            $newQuery->whereColumn(
                $relation->getQualifiedParentKeyName(),
                '=',
                $joinAlias . '.' . $relation->getForeignPivotKeyName()
            );
            $newQuery->limit(1);
        }
        if ($this->hasSoftDeletes($relatedModel)) {
            $newQuery->where($tableAlias . '.' . $relatedModel->getStatusColumn(), '>', 0);
        }

        return $newQuery;
    }

    protected function isRelationField($query, $fieldName) {
        if (method_exists($query->getModel(), $fieldName)) {
            try {
                $query->getModel()->load($fieldName);
            } catch (CModel_Exception_RelationNotFoundException $ex) {
                return false;
            } catch (Exception $ex) {
                return false;
            } catch (Throwable $ex) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null) {
        //do nothing
        $query = $this->getModelQuery($callback);
        //c::db()->enableBenchmark();
        // //create sub query for select * from($q) as t
        // $newQuery = $query->getModel()->newQuery();
        // $newQuery->withoutGlobalScope(CModel_SoftDelete_Scope::class);
        // $newQuery->fromSub($query, 't')->select('*');

        //$newQuery->disableSoftDelete();
        return $query->paginate($perPage, $columns, $pageName, $page);
    }

    public function first($callback = null) {
        //do nothing
        $query = $this->getModelQuery($callback);
        //c::db()->enableBenchmark();
        return $query->first();
    }

    /**
     * Determine whether a model uses SoftDeletes.
     *
     * @param CModel $model
     *
     * @return bool
     */
    public function hasSoftDeletes(CModel $model) {
        return in_array(CModel_SoftDelete_SoftDeleteTrait::class, c::classUsesRecursive($model));
    }

    public function queryCallback($callback) {
        $this->queryCallback = $callback;

        return $this;
    }

    public function toEnumerable() {
        $query = $this->getModelQuery();

        return $query->get();
    }

    /**
     * @param string $method
     * @param string $column
     *
     * @return mixed
     */
    public function aggregate($method, $column) {
        if (!$this->isValidAggregateMethod($method)) {
            throw new Exception($method . ': is not valid aggregate method');
        }
        $query = $this->getModelQuery();

        return $query->$method($column);
    }
}
