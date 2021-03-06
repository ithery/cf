<?php

trait CModel_Relation_Trait_ComparesRelatedModels {
    /**
     * Determine if the model is the related instance of the relationship.
     *
     * @param null|\CModel $model
     *
     * @return bool
     */
    public function is($model) {
        return !is_null($model)
               && $this->compareKeys($this->getParentKey(), $this->getRelatedKeyFrom($model))
               && $this->related->getTable() === $model->getTable()
               && $this->related->getConnectionName() === $model->getConnectionName();
    }

    /**
     * Determine if the model is not the related instance of the relationship.
     *
     * @param null|\CModel $model
     *
     * @return bool
     */
    public function isNot($model) {
        return !$this->is($model);
    }

    /**
     * Get the value of the parent model's key.
     *
     * @return mixed
     */
    abstract public function getParentKey();

    /**
     * Get the value of the model's related key.
     *
     * @param CModel $model
     *
     * @return mixed
     */
    abstract protected function getRelatedKeyFrom(CModel $model);

    /**
     * Compare the parent key with the related key.
     *
     * @param mixed $parentKey
     * @param mixed $relatedKey
     *
     * @return bool
     */
    protected function compareKeys($parentKey, $relatedKey) {
        if (empty($parentKey) || empty($relatedKey)) {
            return false;
        }

        if (is_int($parentKey) || is_int($relatedKey)) {
            return (int) $parentKey === (int) $relatedKey;
        }

        return $parentKey === $relatedKey;
    }
}
