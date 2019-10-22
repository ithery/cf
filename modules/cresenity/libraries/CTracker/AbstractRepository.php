<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:27:20 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CTracker_AbstractRepository implements CTracker_RepositoryInterface {

    protected $builder;
    protected $model;
    protected $result;
    protected $connection;
    protected $className;
    protected $relations;

    /**
     * @var CTracker_Cache
     */
    protected $cache;

    public function __construct() {
        $this->connection = CDatabase::instance();
        $this->cache = new CTracker_Cache();
    }

    public function where($key, $operation, $value = null) {
        $this->builder = $this->builder ?: $this->newQuery();
        $this->builder = $this->builder->where($key, $operation, $value = null);
        return $this;
    }

    public function first() {
        $this->result = $this->builder->first();
        return $this->result ? $this : null;
    }

    public function find($id) {
        list($model, $cacheKey) = $this->cache->findCached($id, null, $this->className);
        if (!$model) {
            $model = $this->newQuery();
            if ($this->relations) {
                $model->with($this->relations);
            }
            if ($model = $model->find($id)) {
                $this->cache->cachePut($cacheKey, $model);
            }
        }
        $this->model = $model;
        $this->result = $model;
        return $model;
    }

    public function create($attributes, $model = null) {
        $model = $model && !$model->exists() ? $model : $this->newModel($model);
        if (!is_array($attributes)) {
            throw new CTracker_Exception('attributes must array');
        }
        foreach ($attributes as $attribute => $value) {
            if (in_array($attribute, $model->getFillable())) {
                $model->{$attribute} = $value;
            }
        }
        $model->save();
        return $model;
    }

    public function getId() {
        return $this->model->getKey();
    }

    /**
     * @param string $attribute
     */
    public function getAttribute($attribute) {
        return $this->result ? $this->result->{$attribute} : null;
    }

    public function setAttribute($attribute, $value) {
        return $this->result->{$attribute} = $value;
    }

    public function save() {
        return $this->result->save();
    }

    /**
     * @param string[] $keys
     */
    public function findOrCreate($attributes, $keys = null, &$created = false, $otherModel = null) {
        list($model, $cacheKey) = $this->cache->findCached($attributes, $keys, $this->className);

        if (!$model) {
            $model = $this->newQuery($otherModel);

            $keys = $keys ?: array_keys($attributes);
            foreach ($keys as $key) {
                $model = $model->where($key, $attributes[$key]);
            }

            if (!$model = $model->first()) {
                $model = $this->create($attributes, $otherModel);
                $created = true;
            }
            $this->cache->cachePut($cacheKey, $model);
        }
        $this->model = $model;

        return $model->getKey();
    }

    public function getModel() {
        if ($this->model == null) {
            $this->newModel();
        }
        if ($this->model instanceof CModel_Query) {
            $this->model = new $this->className();
        }
        if ($this->connection) {
            $this->model->setConnection($this->connection->getName());
        }
        return $this->model;
    }

    public function createModel($modelClass = null) {

        $className = $this->className;

        $this->model = new $className();
        return $this->getModel();
    }

    public function newModel($model = null) {
        $className = $this->className;
        if ($model) {
            $className = get_class($model);
        }
       
        $this->model = new $className();
        return $this->getModel();
    }

    public function newQuery($model = null) {
        $className = $this->className;
        if ($model) {
            $className = get_class($model);
        }
        $this->builder = new $className();
        if ($this->connection) {
            $this->builder = $this->builder->on($this->connection->getName());
        }
        return $this->builder->newQuery();
    }

}
