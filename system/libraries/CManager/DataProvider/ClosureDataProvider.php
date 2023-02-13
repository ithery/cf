<?php

use Opis\Closure\SerializableClosure;

class CManager_DataProvider_ClosureDataProvider extends CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    protected $connection = '';

    /**
     * @var SerializableClosure
     */
    protected $closure;

    protected $requires;

    public function __construct($closure, array $requires = []) {
        $this->closure = new SerializableClosure($closure);
        $this->requires = $requires;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function getConnection() {
        return $this->connection ?: 'default';
    }

    public function toEnumerable() {
        return c::collect($this->closure->__invoke($this->createParameter()));
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null) {
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $parameter = $this->createParameter();
        $parameter->setForPagination($page, $perPage);
        foreach ($this->requires as $require) {
            require_once $require;
        }

        $results = $this->closure->__invoke($parameter);

        if ($results instanceof CPagination_LengthAwarePaginator) {
            return $results;
        }
        $total = 0;
        if (is_array($results)) {
            $total = count($results);
            $results = c::collect($results);
        }

        if (!$results instanceof CCollection) {
            $results = c::collect($results);
        }

        return c::paginator($results, $total, $perPage, $page, [
            'path' => CPagination_Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param string $method
     * @param string $column
     *
     * @return mixed
     */
    public function aggregate($method, $column) {
        // if (!$this->isValidAggregateMethod($method)) {
        //     throw new Exception($method . ': is not valid aggregate method');
        // }

        throw new Exception('Not implemented');
    }
}
