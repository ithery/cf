<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 6, 2019, 7:25:37 AM
 */
class CResources_Repository {
    /** @var CApp_Model_Interface_ResourceInterface|CModel */
    protected $model;

    /** @param CApp_Model_Interface_ResourceInterface $model */
    public function __construct(CApp_Model_Interface_ResourceInterface $model = null) {
        if ($model == null) {
            $model = new CApp_Model_Resource();
        }
        $this->model = $model;
    }

    /**
     * Get all resource in the collection.
     *
     * @param CModel_HasResourceInterface $model
     * @param string                      $collectionName
     * @param array|callable              $filter
     *
     * @return CCollection
     */
    public function getCollection(CModel_HasResourceInterface $model, $collectionName, $filter = []) {
        return $this->applyFilterToResourceCollection($model->loadResource($collectionName), $filter);
    }

    /**
     * Apply given filters on resource.
     *
     * @param CCollection    $resource
     * @param array|callable $filter
     *
     * @return CCollection
     */
    protected function applyFilterToResourceCollection(CCollection $resource, $filter) {
        if (is_array($filter)) {
            $filter = $this->getDefaultFilterFunction($filter);
        }
        return $resource->filter($filter);
    }

    public function all() {
        return $this->model->all();
    }

    public function getByModelType($modelType) {
        return $this->model->where('model_type', $modelType)->get();
    }

    public function getByIds($ids) {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function getByModelTypeAndCollectionName($modelType, $collectionName) {
        return $this->model
            ->where('model_type', $modelType)
            ->where('collection_name', $collectionName)
            ->get();
    }

    public function getByCollectionName($collectionName) {
        return $this->model
            ->where('collection_name', $collectionName)
            ->get();
    }

    /**
     * Convert the given array to a filter function.
     *
     * @param $filters
     *
     * @return \Closure
     */
    protected function getDefaultFilterFunction(array $filters) {
        return function (CApp_Model_Interface_ResourceInterface $resource) use ($filters) {
            foreach ($filters as $property => $value) {
                if (!carr::has($resource->custom_properties, $property)) {
                    return false;
                }
                if (carr::get($resource->custom_properties, $property) !== $value) {
                    return false;
                }
            }
            return true;
        };
    }
}
