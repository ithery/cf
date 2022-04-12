<?php

use Opis\Closure\SerializableClosure;

class CManager_DataProvider_ModelDataProvider extends CManager_DataProviderAbstract {
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
                    $statement = $col->getValue();
                    //$regex = '/([\w]++)`?+(?:\s++as\s++[^,\s]++)?+\s*+(?:FROM\s*+|$)/i';
                    $regex = '/([\w]++)`?+\s*+(?:FROM\s*+|$)/i';

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
    protected function getModelQuery($callback = null) {
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
                    if (strpos($fieldName, '.') !== false) {
                        $fields = explode('.', $fieldName);

                        $field = array_pop($fields);
                        $relation = implode('.', $fields);

                        $q->orWhereHas($relation, function ($q2) use ($value, $field) {
                            $q2->where($field, 'like', '%' . $value . '%');
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
            });
        }

        if (count($this->searchAnd) > 0) {
            $dataSearch = $this->searchAnd;
            $query->where(function (CModel_Query $q) use ($dataSearch, $aggregateFields) {
                foreach ($dataSearch as $fieldName => $value) {
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
            });
        }

        //process ordering
        if (count($this->sort) > 0) {
            $query->getQuery()->orders = null;

            foreach ($this->sort as $fieldName => $sortDirection) {
                if (strpos($fieldName, '.') !== false) {
                    $fields = explode('.', $fieldName);

                    $field = array_pop($fields);
                    $relationPath = implode('.', $fields);
                    $alias = $this->joinWithRelationPath($query, $relationPath, $field);

                    // $query->with([$relationPath => function ($q2) use ($sortDirection, $field) {
                    //     if (!$this->isRelationField($q2, $field)) {
                    //         $q2->orderBy($field, $sortDirection);
                    //     }
                    // }]);
                    $query->orderBy($alias, $sortDirection);
                } else {
                    if (!$this->isRelationField($query, $fieldName)) {
                        $query->orderBy($fieldName, $sortDirection);
                    }
                }
            }
        }

        return $query;
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

    protected function joinWithRelationPath($query, $relationPath, $column) {
        $subQueries = [];
        //$relations = explode('.', $relationPath);
        $relation = $query->getModel()->$relationPath();
        $relationIndex = 0;
        $relatedModel = $relation->getRelated();
        /**
         * @var string
         */
        $relatedTable = $relatedModel->getTable();

        $tableAliasPrefix = 'mdp_' . $this->randomStringAlpha(3);
        $alias = 'mdp_sub_' . $this->randomStringAlpha(3);
        if ($relation instanceof CModel_Relation_BelongsTo) {
            $tableAlias = $tableAliasPrefix . $relationIndex;
            $tableAndAlias = $relatedTable . ' AS ' . $tableAlias;
            if (!isset($subQueries[$column])) {
                $subQueries[$alias] = $currentQuery = c::db()->createQueryBuilder()
                    ->from($tableAndAlias)
                    ->whereColumn(
                        $relation->getQualifiedForeignKeyName(), // 'child-table.fk-column'
                        '=',
                        $tableAlias . '.' . $relation->getOwnerKeyName()  // 'parent-table.id-column'
                    )
                    ->select($tableAlias . '.' . $column);

                if (is_string($column)) {
                    $query->selectSub($currentQuery, $alias);
                } else {
                    throw new \InvalidArgumentException('Columns must be an associative array');
                }
            } else {
                throw new \Exception('Multiple relation chain not implemented yet');
            }
        }

        return $alias;
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

        return $query->paginate($perPage, $columns, $pageName, $page);
    }

    public function queryCallback($callback) {
        $this->queryCallback = $callback;

        return $this;
    }

    public function toEnumerable() {
        $query = $this->getModelQuery();

        return $query->get();
    }
}
