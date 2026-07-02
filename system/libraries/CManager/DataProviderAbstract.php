<?php

abstract class CManager_DataProviderAbstract {
    /**
     * @var array
     */
    protected $searchAnd = [];

    /**
     * @var array
     */
    protected $searchOr = [];

    /**
     * @var array
     */
    protected $searchFullTextOr = [];

    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @var CElement_Depends_DependsOn[]
     */
    protected $callbacks = [];

    /**
     * @param null|int   $perPage
     * @param array      $columns
     * @param string     $pageName
     * @param null|int   $page
     * @param null|mixed $callback
     *
     * @return mixed
     */
    abstract public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null);

    /**
     * @param array $search
     *
     * @return void
     */
    public function searchAnd(array $search) {
        $this->searchAnd = $search;
    }

    /**
     * @param array $search
     *
     * @return void
     */
    public function searchOr(array $search) {
        $this->searchOr = $search;
    }

    /**
     * @param array $search
     *
     * @return void
     */
    public function searchFullTextOr(array $search) {
        $this->searchFullTextOr = $search;
    }

    /**
     * @param array $sort
     *
     * @return void
     */
    public function sort(array $sort) {
        $this->sort = $sort;
    }

    /**
     * @return CManager_DataProviderParameter
     */
    public function createParameter() {
        return new CManager_DataProviderParameter($this->searchAnd, $this->searchOr, $this->sort);
    }

    /**
     * @param mixed $callable
     *
     * @return bool
     */
    protected function isCallable($callable) {
        if (is_string($callable)) {
            return false;
        }

        return is_callable($callable) || ($callable instanceof Opis\Closure\SerializableClosure) || ($callable instanceof CFunction_SerializableClosure);
    }

    /**
     * @param callable $callable
     * @param array    $args
     *
     * @return mixed
     */
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

    /**
     * @param string $method
     *
     * @return bool
     */
    protected function isValidAggregateMethod($method) {
        $validAggregate = ['sum', 'avg', 'min', 'max', 'count'];

        return in_array($method, $validAggregate);
    }

    /**
     * @return CInterface_Enumerable
     */
    abstract public function toEnumerable();
}
