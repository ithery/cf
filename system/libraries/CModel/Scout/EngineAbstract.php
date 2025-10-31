<?php

abstract class CModel_Scout_EngineAbstract {
    /**
     * Update the given model in the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    abstract public function update($models);

    /**
     * Remove the given model from the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    abstract public function delete($models);

    /**
     * Perform the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return mixed
     */
    abstract public function search(CModel_Scout_Builder $builder);

    /**
     * Perform the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return mixed
     */
    abstract public function paginate(CModel_Scout_Builder $builder, $perPage, $page);

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     *
     * @return \CCollection
     */
    abstract public function mapIds($results);

    /**
     * Map the given results to instances of the given model.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CModel_Collection
     */
    abstract public function map(CModel_Scout_Builder $builder, $results, $model);

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CCollection_LazyCollection
     */
    abstract public function lazyMap(CModel_Scout_Builder $builder, $results, $model);

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     *
     * @return int
     */
    abstract public function getTotalCount($results);

    /**
     * Flush all of the model's records from the engine.
     *
     * @param \CModel $model
     *
     * @return void
     */
    abstract public function flush($model);

    /**
     * Create a search index.
     *
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function createIndex($name, array $options = []);

    /**
     * Delete a search index.
     *
     * @param string $name
     *
     * @return mixed
     */
    abstract public function deleteIndex($name);

    /**
     * Pluck and return the primary keys of the given results using the given key name.
     *
     * @param mixed  $results
     * @param string $key
     *
     * @return \CCollection
     */
    public function mapIdsFrom($results, $key) {
        return $this->mapIds($results);
    }

    /**
     * Get the results of the query as a Collection of primary keys.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return \CCollection
     */
    public function keys(CModel_Scout_Builder $builder) {
        return $this->mapIds($this->search($builder));
    }

    /**
     * Get the results of the given query mapped onto models.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return \CModel_Collection
     */
    public function get(CModel_Scout_Builder $builder) {
        return $this->map(
            $builder,
            $builder->applyAfterRawSearchCallback($this->search($builder)),
            $builder->model
        );
    }

    /**
     * Get a lazy collection for the given query mapped onto models.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return \CModel_Collection
     */
    public function cursor(CModel_Scout_Builder $builder) {
        return $this->lazyMap(
            $builder,
            $builder->applyAfterRawSearchCallback($this->search($builder)),
            $builder->model
        );
    }
}
