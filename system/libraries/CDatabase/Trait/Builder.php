<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @template TModelClass
 */
trait CDatabase_Trait_Builder {
    use CTrait_Conditionable;

    /**
     * Chunk the results of the query.
     *
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk($count, callable $callback) {
        $this->enforceOrderBy();
        $page = 1;
        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $this->forPage($page, $count)->get();
            $countResults = $results->count();
            if ($countResults == 0) {
                break;
            }
            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }
            unset($results);
            $page++;
        } while ($countResults == $count);

        return true;
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param callable $callback
     * @param int      $count
     *
     * @return bool
     */
    public function each(callable $callback, $count = 1000) {
        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }
        });
    }

    /**
     * Chunk the results of a query by comparing IDs.
     *
     * @param int         $count
     * @param callable    $callback
     * @param null|string $column
     * @param null|string $alias
     *
     * @return bool
     */
    public function chunkById($count, callable $callback, $column = null, $alias = null) {
        if ($column == null) {
            $column = $this->defaultKeyName();
        }

        if ($alias == null) {
            $alias = $column;
        }

        $lastId = null;

        $page = 1;

        do {
            $clone = clone $this;

            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $clone->forPageAfterId($count, $lastId, $column)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }

            $lastId = $results->last()->{$alias};

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

    /**
     * Execute a callback over each item while chunking by ID.
     *
     * @param callable    $callback
     * @param int         $count
     * @param null|string $column
     * @param null|string $alias
     *
     * @return bool
     */
    public function eachById(callable $callback, $count = 1000, $column = null, $alias = null) {
        return $this->chunkById($count, function ($results, $page) use ($callback, $count) {
            foreach ($results as $key => $value) {
                if ($callback($value, (($page - 1) * $count) + $key) === false) {
                    return false;
                }
            }
        }, $column, $alias);
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param int $chunkSize
     *
     * @throws \InvalidArgumentException
     *
     * @return \CCollection_LazyCollection
     */
    public function lazy($chunkSize = 1000) {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The chunk size should be at least 1');
        }

        $this->enforceOrderBy();

        return CCollection_LazyCollection::make(function () use ($chunkSize) {
            $page = 1;

            while (true) {
                $results = $this->forPage($page++, $chunkSize)->get();

                foreach ($results as $result) {
                    yield $result;
                }

                if ($results->count() < $chunkSize) {
                    return;
                }
            }
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs.
     *
     * @param int         $chunkSize
     * @param null|string $column
     * @param null|string $alias
     *
     * @throws \InvalidArgumentException
     *
     * @return \CCollection_LazyCollection
     */
    public function lazyById($chunkSize = 1000, $column = null, $alias = null) {
        return $this->orderedLazyById($chunkSize, $column, $alias);
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in descending order.
     *
     * @param int         $chunkSize
     * @param null|string $column
     * @param null|string $alias
     *
     * @throws \InvalidArgumentException
     *
     * @return \CCollection_LazyCollection
     */
    public function lazyByIdDesc($chunkSize = 1000, $column = null, $alias = null) {
        return $this->orderedLazyById($chunkSize, $column, $alias, true);
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in a given order.
     *
     * @param int         $chunkSize
     * @param null|string $column
     * @param null|string $alias
     * @param bool        $descending
     *
     * @throws \InvalidArgumentException
     *
     * @return \CCollection_LazyCollection
     */
    protected function orderedLazyById($chunkSize = 1000, $column = null, $alias = null, $descending = false) {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The chunk size should be at least 1');
        }

        if ($column == null) {
            $column = $this->defaultKeyName();
        }

        if ($alias == null) {
            $alias = $column;
        }

        return CCollection_LazyCollection::make(function () use ($chunkSize, $column, $alias, $descending) {
            $lastId = null;

            while (true) {
                $clone = clone $this;

                if ($descending) {
                    $results = $clone->forPageBeforeId($chunkSize, $lastId, $column)->get();
                } else {
                    $results = $clone->forPageAfterId($chunkSize, $lastId, $column)->get();
                }

                foreach ($results as $result) {
                    yield $result;
                }

                if ($results->count() < $chunkSize) {
                    return;
                }

                $lastId = $results->last()->{$alias};
            }
        });
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @return null|CModel|object|static
     *
     * @phpstan-return TModelClass|null
     */
    public function first($columns = ['*']) {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Execute the query and get the first result if it's the sole matching record.
     *
     * @param array|string $columns
     *
     * @throws \CDatabase_Exception_RecordsNotFoundException
     * @throws \CDatabase_Exception_MultipleRecordsFoundException
     *
     * @return null|\CModel|object|static
     */
    public function sole($columns = ['*']) {
        $result = $this->take(2)->get($columns);

        if ($result->isEmpty()) {
            throw new CDatabase_Exception_RecordsNotFoundException();
        }

        if ($result->count() > 1) {
            throw new CDatabase_Exception_MultipleRecordsFoundException();
        }

        return $result->first();
    }

    /**
     * Apply the callback's query changes if the given "value" is true.
     *
     * @param mixed    $value
     * @param callable $callback
     * @param callable $default
     *
     * @return mixed|$this
     */
    public function when($value, $callback, $default = null) {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Pass the query to a given callback.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function tap($callback) {
        return $this->when(true, $callback);
    }

    /**
     * Apply the callback's query changes if the given "value" is false.
     *
     * @param mixed    $value
     * @param callable $callback
     * @param callable $default
     *
     * @return mixed
     */
    public function unless($value, $callback, $default = null) {
        if (!$value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param CCollection $items
     * @param int         $total
     * @param int         $perPage
     * @param int         $currentPage
     * @param array       $options
     *
     * @return CPagination_LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options) {
        return CContainer::getInstance()->makeWith(CPagination_LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }

    /**
     * Create a new simple paginator instance.
     *
     * @param CCollection $items
     * @param int         $perPage
     * @param int         $currentPage
     * @param array       $options
     *
     * @return CPagination_Paginator
     */
    protected function simplePaginator($items, $perPage, $currentPage, $options) {
        return CContainer::getInstance()->makeWith(CPagination_Paginator::class, compact(
            'items',
            'perPage',
            'currentPage',
            'options'
        ));
    }
}
