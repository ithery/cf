<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:13:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CQueue_Trait_SerializesAndRestoresModelIdentifiers {

    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getSerializedPropertyValue($value) {
        if ($value instanceof QueueableCollection) {
            return new ModelIdentifier(
                    $value->getQueueableClass(), $value->getQueueableIds(), $value->getQueueableConnection()
            );
        }

        if ($value instanceof QueueableEntity) {
            return new ModelIdentifier(
                    get_class($value), $value->getQueueableId(), $value->getQueueableConnection()
            );
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value) {
        if (!$value instanceof ModelIdentifier) {
            return $value;
        }

        return is_array($value->id) ? $this->restoreCollection($value) : $this->getQueryForModelRestoration((new $value->class)->setConnection($value->connection), $value->id)
                        ->useWritePdo()->firstOrFail();
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param  \Illuminate\Contracts\Database\ModelIdentifier  $value
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function restoreCollection($value) {
        if (!$value->class || count($value->id) === 0) {
            return new EloquentCollection;
        }

        return $this->getQueryForModelRestoration(
                        (new $value->class)->setConnection($value->connection), $value->id
                )->useWritePdo()->get();
    }

    /**
     * Get the query for restoration.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array|int                            $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQueryForModelRestoration($model, $ids) {
        return $model->newQueryForRestoration($ids);
    }

}
