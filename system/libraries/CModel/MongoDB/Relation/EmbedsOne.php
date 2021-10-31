<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 6, 2019, 5:20:00 PM
 */
use MongoDB\BSON\ObjectID;

class CModel_MongoDB_Relation_EmbedsOne extends CModel_MongoDB_Relation_EmbedsOneOrMany {
    /**
     * @inheritdoc
     */
    public function initRelation(array $models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }

    /**
     * @inheritdoc
     */
    public function getResults() {
        return $this->toModel($this->getEmbedded());
    }

    /**
     * Save a new model and attach it to the parent model.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model|bool
     */
    public function performInsert(CModel_MongoDB_Model $model) {
        // Generate a new key if needed.
        if ($model->getKeyName() == '_id' && !$model->getKey()) {
            $model->setAttribute('_id', new ObjectID);
        }
        // For deeply nested documents, let the parent handle the changes.
        if ($this->isNested()) {
            $this->associate($model);
            return $this->parent->save() ? $model : false;
        }
        $result = $this->getBaseQuery()->update([$this->localKey => $model->getAttributes()]);
        // Attach the model to its parent.
        if ($result) {
            $this->associate($model);
        }
        return $result ? $model : false;
    }

    /**
     * Save an existing model and attach it to the parent model.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model|bool
     */
    public function performUpdate(CModel_MongoDB_Model $model) {
        if ($this->isNested()) {
            $this->associate($model);
            return $this->parent->save();
        }
        $values = $this->getUpdateValues($model->getDirty(), $this->localKey . '.');
        $result = $this->getBaseQuery()->update($values);
        // Attach the model to its parent.
        if ($result) {
            $this->associate($model);
        }
        return $result ? $model : false;
    }

    /**
     * Delete an existing model and detach it from the parent model.
     *
     * @return int
     */
    public function performDelete() {
        // For deeply nested documents, let the parent handle the changes.
        if ($this->isNested()) {
            $this->dissociate();
            return $this->parent->save();
        }
        // Overwrite the local key with an empty array.
        $result = $this->getBaseQuery()->update([$this->localKey => null]);
        // Detach the model from its parent.
        if ($result) {
            $this->dissociate();
        }
        return $result;
    }

    /**
     * Attach the model to its parent.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model
     */
    public function associate(CModel_MongoDB_Model $model) {
        return $this->setEmbedded($model->getAttributes());
    }

    /**
     * Detach the model from its parent.
     *
     * @return CModel_MongoDB_Model
     */
    public function dissociate() {
        return $this->setEmbedded(null);
    }

    /**
     * Delete all embedded models.
     *
     * @return int
     */
    public function delete() {
        return $this->performDelete();
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param CModel $model
     * @param string $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return 'whereIn';
    }
}
