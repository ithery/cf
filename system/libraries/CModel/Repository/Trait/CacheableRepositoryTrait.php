<?php

use Exception;
use ReflectionObject;
use Prettus\Repository\Helpers\CacheKeys;
use Prettus\Repository\Contracts\CriteriaInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Class CacheableRepository.
 */
trait CModel_Repository_Trait_CacheableRepositoryTrait {
    /**
     * @var CCache_RepositoryInterface
     */
    protected $cacheRepository = null;

    /**
     * Set Cache Repository.
     *
     * @param CCache_RepositoryInterface $repository
     *
     * @return $this
     */
    public function setCacheRepository(CCache_RepositoryInterface $repository) {
        $this->cacheRepository = $repository;

        return $this;
    }

    /**
     * Return instance of Cache Repository.
     *
     * @return CCache_RepositoryInterface
     */
    public function getCacheRepository() {
        if (is_null($this->cacheRepository)) {
            $this->cacheRepository = CContainer::getInstance()->make(CF::config('repository.cache.repository', 'cache'));
        }

        return $this->cacheRepository;
    }

    /**
     * Skip Cache.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCache($status = true) {
        $this->cacheSkip = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSkippedCache() {
        $skipped = isset($this->cacheSkip) ? $this->cacheSkip : false;
        $request = CHTTP::request();
        $skipCacheParam = CF::config('repository.cache.params.skipCache', 'skipCache');

        if ($request->has($skipCacheParam) && $request->get($skipCacheParam)) {
            $skipped = true;
        }

        return $skipped;
    }

    /**
     * @param $method
     *
     * @return bool
     */
    protected function allowedCache($method) {
        $cacheEnabled = CF::config('repository.cache.enabled', true);

        if (!$cacheEnabled) {
            return false;
        }

        $cacheOnly = isset($this->cacheOnly) ? $this->cacheOnly : CF::config('repository.cache.allowed.only', null);
        $cacheExcept = isset($this->cacheExcept) ? $this->cacheExcept : CF::config('repository.cache.allowed.except', null);

        if (is_array($cacheOnly)) {
            return in_array($method, $cacheOnly);
        }

        if (is_array($cacheExcept)) {
            return !in_array($method, $cacheExcept);
        }

        if (is_null($cacheOnly) && is_null($cacheExcept)) {
            return true;
        }

        return false;
    }

    /**
     * Get Cache key for the method.
     *
     * @param $method
     * @param $args
     *
     * @return string
     */
    public function getCacheKey($method, $args = null) {
        $request = CHTTP::request();
        $args = serialize($args);
        $criteria = $this->serializeCriteria();
        $key = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria . $request->fullUrl()));

        CModel_Repository_Helper_CacheKeys::putKey(get_called_class(), $key);

        return $key;
    }

    /**
     * Serialize the criteria making sure the Closures are taken care of.
     *
     * @return string
     */
    protected function serializeCriteria() {
        try {
            return serialize($this->getCriteria());
        } catch (Exception $e) {
            return serialize($this->getCriteria()->map(function ($criterion) {
                return $this->serializeCriterion($criterion);
            }));
        }
    }

    /**
     * Serialize single criterion with customized serialization of Closures.
     *
     * @param \Prettus\Repository\Contracts\CriteriaInterface $criterion
     *
     * @throws \Exception
     *
     * @return \Prettus\Repository\Contracts\CriteriaInterface|array
     */
    protected function serializeCriterion($criterion) {
        try {
            serialize($criterion);

            return $criterion;
        } catch (Exception $e) {
            // We want to take care of the closure serialization errors,
            // other than that we will simply re-throw the exception.
            if ($e->getMessage() !== "Serialization of 'Closure' is not allowed") {
                throw $e;
            }

            $r = new ReflectionObject($criterion);

            return [
                'hash' => md5((string) $r),
            ];
        }
    }

    /**
     * Get cache time.
     *
     * Return minutes: version < 5.8
     * Return seconds: version >= 5.8
     *
     * @return int
     */
    public function getCacheTime() {
        $cacheMinutes = isset($this->cacheMinutes) ? $this->cacheMinutes : CF::config('repository.cache.minutes', 30);

        return $cacheMinutes * 60;
    }

    /**
     * Retrieve all data of repository.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']) {
        if (!$this->allowedCache('all') || $this->isSkippedCache()) {
            return parent::all($columns);
        }

        $key = $this->getCacheKey('all', func_get_args());
        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($columns) {
            return parent::all($columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Retrieve all data of repository, paginated.
     *
     * @param null   $limit
     * @param array  $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = 'paginate') {
        if (!$this->allowedCache('paginate') || $this->isSkippedCache()) {
            return parent::paginate($limit, $columns, $method);
        }

        $key = $this->getCacheKey('paginate', func_get_args());

        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($limit, $columns, $method) {
            return parent::paginate($limit, $columns, $method);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by id.
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']) {
        if (!$this->allowedCache('find') || $this->isSkippedCache()) {
            return parent::find($id, $columns);
        }

        $key = $this->getCacheKey('find', func_get_args());
        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($id, $columns) {
            return parent::find($id, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by field and value.
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*']) {
        if (!$this->allowedCache('findByField') || $this->isSkippedCache()) {
            return parent::findByField($field, $value, $columns);
        }

        $key = $this->getCacheKey('findByField', func_get_args());
        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($field, $value, $columns) {
            return parent::findByField($field, $value, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by multiple fields.
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']) {
        if (!$this->allowedCache('findWhere') || $this->isSkippedCache()) {
            return parent::findWhere($where, $columns);
        }

        $key = $this->getCacheKey('findWhere', func_get_args());
        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($where, $columns) {
            return parent::findWhere($where, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by Criteria.
     *
     * @param CModel_Repository_Contract_CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CModel_Repository_Contract_CriteriaInterface $criteria) {
        if (!$this->allowedCache('getByCriteria') || $this->isSkippedCache()) {
            return parent::getByCriteria($criteria);
        }

        $key = $this->getCacheKey('getByCriteria', func_get_args());
        $time = $this->getCacheTime();
        $value = $this->getCacheRepository()->remember($key, $time, function () use ($criteria) {
            return parent::getByCriteria($criteria);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }
}
