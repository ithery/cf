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
     * @return CModel_Query
     */
    protected function getModelQuery() {
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

        //process search
        if (count($this->searchOr) > 0) {
            $dataSearch = $this->searchOr;
            $query->where(function (CModel_Query $q) use ($dataSearch) {
                foreach ($dataSearch as $fieldName => $value) {
                    if (strpos($fieldName, '.') !== false) {
                        $fields = explode('.', $fieldName);

                        $field = array_pop($fields);
                        $relation = implode('.', $fields);

                        $q->orWhereHas($relation, function ($q2) use ($value, $field) {
                            $q2->where($field, 'like', '%' . $value . '%');
                        });
                    } else {
                        $q->orWhere($fieldName, 'like', '%' . $value . '%');
                    }
                }
            });
        }

        if (count($this->searchAnd) > 0) {
            $dataSearch = $this->searchAnd;
            $query->where(function (CModel_Query $q) use ($dataSearch) {
                foreach ($dataSearch as $fieldName => $value) {
                    if (strpos($fieldName, '.') !== false) {
                        $fields = explode('.', $fieldName);

                        $field = array_pop($fields);
                        $relation = implode('.', $fields);

                        $q->WhereHas($relation, function ($q2) use ($value, $field) {
                            $q2->where($field, 'like', '%' . $value . '%');
                        });
                    } else {
                        $q->Where($fieldName, 'like', '%' . $value . '%');
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
                    $relation = implode('.', $fields);

                    $query->with([$relation => function ($q2) use ($sortDirection, $field) {
                        $q2->orderBy($field, $sortDirection);
                    }]);
                } else {
                    $query->orderBy($fieldName, $sortDirection);
                }
            }
        }

        return $query;
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null) {
        $query = $this->getModelQuery();

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
