<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 1:15:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CDatabase_Trait_Builder {

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
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
     * @param  callable  $callback
     * @param  int  $count
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
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return CModel|object|static|null
     */
    public function first($columns = ['*']) {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Apply the callback's query changes if the given "value" is true.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable  $default
     * @return mixed
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
     * @param  \Closure  $callback
     * @return CDatabase_Query_Builder
     */
    public function tap($callback) {
        return $this->when(true, $callback);
    }

    /**
     * Apply the callback's query changes if the given "value" is false.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable  $default
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
     * @param  CCollection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return CPagination_LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options) {
        return CContainer::getInstance()->makeWith(CPagination_LengthAwarePaginator::class, compact(
                                'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Create a new simple paginator instance.
     *
     * @param  CCollection  $items
     * @param  int $perPage
     * @param  int $currentPage
     * @param  array  $options
     * @return CPagination_Paginator
     */
    protected function simplePaginator($items, $perPage, $currentPage, $options) {
        return CContainer::getInstance()->makeWith(CPagination_Paginator::class, compact(
                                'items', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Set the limit and offset for a given page.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return CDatabase_Query_Builder|static
     */
    public function forPage($page, $perPage = 15) {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

}
