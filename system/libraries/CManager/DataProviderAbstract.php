<?php

abstract class CManager_DataProviderAbstract {
    protected $searchAnd = [];

    protected $searchOr = [];

    protected $searchFullTextOr = [];

    protected $sort = [];

    /**
     * @var CElement_Depends_DependsOn[]
     */
    protected $callbacks = [];

    abstract public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null);

    public function searchAnd(array $search) {
        $this->searchAnd = $search;
    }

    public function searchOr(array $search) {
        $this->searchOr = $search;
    }

    public function searchFullTextOr(array $search) {
        $this->searchFullTextOr = $search;
    }

    public function sort(array $sort) {
        $this->sort = $sort;
    }

    public function createParameter() {
        return new CManager_DataProviderParameter($this->searchAnd, $this->searchOr, $this->sort);
    }

    protected function isCallable($callable) {
        if (is_string($callable)) {
            return false;
        }

        return is_callable($callable) || ($callable instanceof Opis\Closure\SerializableClosure) || ($callable instanceof CFunction_SerializableClosure);
    }

    protected function callCallable($callable, array $args = []) {
        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        }
        if ($callable instanceof Opis\Closure\SerializableClosure) {
            return $callable->__invoke(...$args);
        }
        if ($callable instanceof CFunction_SerializableClosure) {
            return $callable->__invoke(...$args);
        }

        throw new Exception('Cannot call callable on Data Provider');
    }

    protected function isValidAggregateMethod($method) {
        $validAggregate = ['sum', 'avg', 'min', 'max', 'count'];

        return in_array($method, $validAggregate);
    }

    abstract public function toEnumerable();
}
