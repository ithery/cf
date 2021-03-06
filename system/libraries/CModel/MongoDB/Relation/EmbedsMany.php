<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 *
 * @since Oct 21, 2019, 9:23:35 PM
 *
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use MongoDB\BSON\ObjectID;

class CModel_MongoDB_Relation_EmbedsMany extends CModel_MongoDB_Relation_EmbedsOneOrMany {
    /**
     * @inheritdoc
     */
    public function initRelation(array $models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * @inheritdoc
     */
    public function getResults() {
        return $this->toCollection($this->getEmbedded());
    }

    /**
     * Save a new model and attach it to the parent model.
     *
     * @param CModel $model
     *
     * @return CModel|bool
     */
    public function performInsert(CModel_MongoDB_Model $model) {
        // Generate a new key if needed.
        if ($model->getKeyName() == '_id' && !$model->getKey()) {
            $model->setAttribute('_id', new ObjectID());
        }
        // For deeply nested documents, let the parent handle the changes.
        if ($this->isNested()) {
            $this->associate($model);

            return $this->parent->save() ? $model : false;
        }
        // Push the new model to the database.
        $result = $this->getBaseQuery()->push($this->localKey, $model->getAttributes(), true);
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
        // For deeply nested documents, let the parent handle the changes.
        if ($this->isNested()) {
            $this->associate($model);

            return $this->parent->save();
        }
        // Get the correct foreign key value.
        $foreignKey = $this->getForeignKeyValue($model);
        $values = $this->getUpdateValues($model->getDirty(), $this->localKey . '.$.');
        // Update document in database.
        $result = $this->getBaseQuery()->where($this->localKey . '.' . $model->getKeyName(), $foreignKey)
            ->update($values);
        // Attach the model to its parent.
        if ($result) {
            $this->associate($model);
        }

        return $result ? $model : false;
    }

    /**
     * Delete an existing model and detach it from the parent model.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return int
     */
    public function performDelete(CModel_MongoDB_Model $model) {
        // For deeply nested documents, let the parent handle the changes.
        if ($this->isNested()) {
            $this->dissociate($model);

            return $this->parent->save();
        }
        // Get the correct foreign key value.
        $foreignKey = $this->getForeignKeyValue($model);
        $result = $this->getBaseQuery()->pull($this->localKey, [$model->getKeyName() => $foreignKey]);
        if ($result) {
            $this->dissociate($model);
        }

        return $result;
    }

    /**
     * Associate the model instance to the given parent, without saving it to the database.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model
     */
    public function associate(CModel_MongoDB_Model $model) {
        if (!$this->contains($model)) {
            return $this->associateNew($model);
        }

        return $this->associateExisting($model);
    }

    /**
     * Dissociate the model instance from the given parent, without saving it to the database.
     *
     * @param mixed $ids
     *
     * @return int
     */
    public function dissociate($ids = []) {
        $ids = $this->getIdsArrayFrom($ids);
        $records = $this->getEmbedded();
        $primaryKey = $this->related->getKeyName();
        // Remove the document from the parent model.
        foreach ($records as $i => $record) {
            if (in_array($record[$primaryKey], $ids)) {
                unset($records[$i]);
            }
        }
        $this->setEmbedded($records);
        // We return the total number of deletes for the operation. The developers
        // can then check this number as a boolean type value or get this total count
        // of records deleted for logging, etc.
        return count($ids);
    }

    /**
     * Destroy the embedded models for the given IDs.
     *
     * @param mixed $ids
     *
     * @return int
     */
    public function destroy($ids = []) {
        $count = 0;
        $ids = $this->getIdsArrayFrom($ids);
        // Get all models matching the given ids.
        $models = $this->getResults()->only($ids);
        // Pull the documents from the database.
        foreach ($models as $model) {
            if ($model->delete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Delete all embedded models.
     *
     * @return int
     */
    public function delete() {
        // Overwrite the local key with an empty array.
        $result = $this->query->update([$this->localKey => []]);
        if ($result) {
            $this->setEmbedded([]);
        }

        return $result;
    }

    /**
     * Destroy alias.
     *
     * @param mixed $ids
     *
     * @return int
     */
    public function detach($ids = []) {
        return $this->destroy($ids);
    }

    /**
     * Save alias.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model
     */
    public function attach(CModel_MongoDB_Model $model) {
        return $this->save($model);
    }

    /**
     * Associate a new model instance to the given parent, without saving it to the database.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model
     */
    protected function associateNew($model) {
        // Create a new key if needed.
        if ($model->getKeyName() === '_id' && !$model->getAttribute('_id')) {
            $model->setAttribute('_id', new ObjectID());
        }
        $records = $this->getEmbedded();
        // Add the new model to the embedded documents.
        $records[] = $model->getAttributes();

        return $this->setEmbedded($records);
    }

    /**
     * Associate an existing model instance to the given parent, without saving it to the database.
     *
     * @param CModel_MongoDB_Model $model
     *
     * @return CModel_MongoDB_Model
     */
    protected function associateExisting($model) {
        // Get existing embedded documents.
        $records = $this->getEmbedded();
        $primaryKey = $this->related->getKeyName();
        $key = $model->getKey();
        // Replace the document in the parent model.
        foreach ($records as &$record) {
            if ($record[$primaryKey] == $key) {
                $record = $model->getAttributes();

                break;
            }
        }

        return $this->setEmbedded($records);
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param int $perPage
     *
     * @return \CPagination_AbstractPaginator
     */
    public function paginate($perPage = null) {
        $page = CPagination_Paginator::resolveCurrentPage();
        $perPage = $perPage ?: $this->related->getPerPage();
        $results = $this->getEmbedded();
        $total = count($results);
        $start = ($page - 1) * $perPage;
        $sliced = array_slice($results, $start, $perPage);

        return new CPagination_LengthAwarePaginator($sliced, $total, $perPage, $page, [
            'path' => CPagination_Paginator::resolveCurrentPath(),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getEmbedded() {
        return parent::getEmbedded() ?: [];
    }

    /**
     * @inheritdoc
     */
    protected function setEmbedded($models) {
        if (!is_array($models)) {
            $models = [$models];
        }

        return parent::setEmbedded(array_values($models));
    }

    /**
     * @inheritdoc
     */
    public function __call($method, $parameters) {
        if (method_exists(CModel_Collection::class, $method)) {
            return call_user_func_array([$this->getResults(), $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param \CModel $model
     * @param string  $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return 'whereIn';
    }
}
