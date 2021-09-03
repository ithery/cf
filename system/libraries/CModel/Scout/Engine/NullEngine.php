<?php

class CModel_Scout_Engine_NullEngine extends CModel_Scout_EngineAbstract {
    /**
     * Update the given model in the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function update($models) {
        //
    }

    /**
     * Remove the given model from the index.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function delete($models) {
        //
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     *
     * @return mixed
     */
    public function search(CModel_Scout_Builder $builder) {
        return [];
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \CModel_Scout_Builder $builder
     * @param int                   $perPage
     * @param int                   $page
     *
     * @return mixed
     */
    public function paginate(CModel_Scout_Builder $builder, $perPage, $page) {
        return [];
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     *
     * @return \CCollection
     */
    public function mapIds($results) {
        return CCollection::make();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CModel_Collection
     */
    public function map(CModel_Scout_Builder $builder, $results, $model) {
        return CModel_Collection::make();
    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param \CModel_Scout_Builder $builder
     * @param mixed                 $results
     * @param \CModel               $model
     *
     * @return \CBase_LazyCollection
     */
    public function lazyMap(CModel_Scout_Builder $builder, $results, $model) {
        return CBase_LazyCollection::make();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     *
     * @return int
     */
    public function getTotalCount($results) {
        return count($results);
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function flush($model) {
        //
    }

    /**
     * Create a search index.
     *
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    public function createIndex($name, array $options = []) {
        return [];
    }

    /**
     * Delete a search index.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function deleteIndex($name) {
        return [];
    }
}
