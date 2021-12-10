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
        if (count($this->search) > 0) {
            foreach ($this->search as $fieldName => $value) {
                if (strpos($fieldName, '.') !== false) {
                    $fields = explode('.', $fieldName);

                    $field = array_pop($fields);
                    $relation = implode('.', $fields);

                    $query->whereHas($relation, function ($q2) use ($value, $field) {
                        $q2->where($field, 'like', '%' . $value . '%');
                    });
                } else {
                    $query->where($fieldName, 'like', '%' . $value . '%');
                }
            }
        }

        //process ordering
        if (count($this->sort) > 0) {
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

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
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
