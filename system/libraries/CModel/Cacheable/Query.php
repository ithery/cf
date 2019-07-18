<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 4:03:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * @mixin CModel_Query
 */
class CModel_Cacheable_Query extends CModel_Query {

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return CModel_Collection|static[]
     */
    public function get($columns = ['*']) {
        $builder = $this->applyScopes();
        $closure = function () use ($builder, $columns) {
            // If we actually found models we will also eager load any relationships that
            // have been specified as needing to be eager loaded, which will solve the
            // n+1 query issue for the developers to avoid running a lot of queries.
            if (count($models = $builder->getModels($columns)) > 0) {
                $models = $builder->eagerLoadRelations($models);
            }
            return $builder->model->newCollection($models);
        };
        // Check if cache is enabled
        if ($builder->model->getCacheLifetime()) {
            return $builder->model->cacheQuery($builder, $columns, $closure);
        }
        // Cache disabled, just execute query & return result
        $results = call_user_func($closure);
        // We're done, let's clean up!
        $builder->model->resetCacheConfig();
        return $results;
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return CCollection
     */
    public function pluck($column, $key = null) {
        $builder = $this->toBase();
        $closure = function () use ($builder, $column, $key) {
            $results = $builder->pluck($column, $key);
            // If the model has a mutator for the requested column, we will spin through
            // the results and mutate the values so that the mutated version of these
            // columns are returned as you would expect from these Eloquent models.
            if (!$this->model->hasGetMutator($column) &&
                    !$this->model->hasCast($column) &&
                    !in_array($column, $this->model->getDates())) {
                return $results;
            }
            return $results->map(function ($value) use ($column) {
                        return $this->model->newFromBuilder([$column => $value])->{$column};
                    });
        };
        // Check if cache is enabled
        if ($this->model->getCacheLifetime()) {
            return $this->model->cacheQuery($builder, (array) $column, $closure);
        }
        // Cache disabled, just execute query & return result
        $results = call_user_func($closure);
        // We're done, let's clean up!
        $this->model->resetCacheConfig();
        return $results;
    }

}
