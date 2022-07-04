<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:13:39 AM
 */
trait CQueue_Trait_SerializesAndRestoresModelIdentifiers {
    /**
     * Get the property value prepared for serialization.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function getSerializedPropertyValue($value) {
        if ($value instanceof CQueue_QueueableCollectionInterface) {
            return new CModel_Identifier(
                $value->getQueueableClass(),
                $value->getQueueableIds(),
                $value->getQueueableRelations(),
                $value->getQueueableConnection()
            );
        }

        if ($value instanceof CQueue_QueueableEntityInterface) {
            return new CModel_Identifier(
                get_class($value),
                $value->getQueueableId(),
                $value->getQueueableRelations(),
                $value->getQueueableConnection()
            );
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function getRestoredPropertyValue($value) {
        if (!$value instanceof CModel_Identifier) {
            return $value;
        }

        return is_array($value->id)
            ? $this->restoreCollection($value)
            : $this->restoreModel($value);
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param \CModel_Identifier $value
     *
     * @return \CModel_Collection
     */
    protected function restoreCollection($value) {
        if (!$value->class || count($value->id) === 0) {
            return new CModel_Collection();
        }
        $collection = $this->getQueryForModelRestoration(
            (new $value->class())->setConnection($value->connection),
            $value->id
        )->useWritePdo()->get();
        if (is_a($value->class, CModel_Relation_Pivot::class, true)
            || in_array(CModel_Relation_Trait_AsPivot::class, class_uses($value->class))
        ) {
            return $collection;
        }

        $collection = $collection->keyBy->getKey();

        $collectionClass = get_class($collection);

        return new $collectionClass(
            c::collect($value->id)->map(function ($id) use ($collection) {
                return $collection[$id] ?? null;
            })->filter()
        );
    }

    /**
     * Restore the model from the model identifier instance.
     *
     * @param \CModel_Identifier $value
     *
     * @return \CModel
     */
    public function restoreModel($value) {
        return $this->getQueryForModelRestoration(
            (new $value->class())->setConnection($value->connection),
            $value->id
        )->useWritePdo()->firstOrFail()->load(isset($value->relations) ? ($value->relations ?: []) : []);
    }

    /**
     * Get the query for restoration.
     *
     * @param \CModel   $model
     * @param array|int $ids
     *
     * @return \CModel_Query
     */
    protected function getQueryForModelRestoration($model, $ids) {
        return $model->newQueryForRestoration($ids);
    }
}
