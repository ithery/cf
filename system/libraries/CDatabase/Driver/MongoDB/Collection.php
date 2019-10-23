<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 21, 2019, 11:06:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use MongoDB\BSON\ObjectID;
use MongoDB\Collection as MongoCollection;

class CDatabase_Driver_MongoDB_Collection {

    /**
     * The driver instance.
     * @var CDatabase_Driver_MongoDB
     */
    protected $driver;

    /**
     * The connection instance.
     * @var CDatabase
     */
    protected $connection;

    
    /**
     * The MongoCollection instance..
     * @var MongoCollection
     */
    protected $collection;

    /**
     * @param CDatabase_Driver_MongoDB $connection
     * @param MongoCollection $collection
     */
    public function __construct(CDatabase_Driver_MongoDB $driver, MongoCollection $collection) {
        $this->driver = $driver;
        $this->collection = $collection;
    }

    /**
     * Handle dynamic method calls.
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        $start = microtime(true);
        $result = call_user_func_array([$this->collection, $method], $parameters);
        if ($this->driver->db()->isLogQuery()) {
            // Once we have run the query we will calculate the time that it took to run and
            // then log the query, bindings, and execution time so we will report them on
            // the event that the developer needs them. We'll log time in milliseconds.
            $time = $this->connection->getElapsedTime($start);
            $query = [];
            // Convert the query parameters to a json string.
            array_walk_recursive($parameters, function (&$item, $key) {
                if ($item instanceof ObjectID) {
                    $item = (string) $item;
                }
            });
            // Convert the query parameters to a json string.
            foreach ($parameters as $parameter) {
                try {
                    $query[] = json_encode($parameter);
                } catch (Exception $e) {
                    $query[] = '{...}';
                }
            }
            $queryString = $this->collection->getCollectionName() . '.' . $method . '(' . implode(',', $query) . ')';
            $this->driver->db()->logQuery($queryString, [], $time);
        }
        return $result;
    }

}
