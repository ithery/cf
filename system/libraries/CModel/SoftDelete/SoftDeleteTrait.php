<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CModel
 *
 * @method static \CModel_Query<static>|static withTrashed(bool $withTrashed = true)
 * @method static \CModel_Query<static>|static onlyTrashed()
 * @method static \CModel_Query<static>|static withoutTrashed()
 * @method static bool                         restore()
 */
trait CModel_SoftDelete_SoftDeleteTrait {
    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeleteTrait() {
        static::addGlobalScope(new CModel_SoftDelete_Scope());
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * @return null|bool
     */
    public function forceDelete() {
        $this->forceDeleting = true;

        $deleted = $this->delete();

        $this->forceDeleting = false;

        return $deleted;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return mixed
     */
    protected function performDeleteOnModel() {
        if ($this->forceDeleting) {
            $this->exists = false;

            return $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey())->forceDelete();
        }

        return $this->runSoftDelete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete() {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());

        $time = $this->freshTimestamp();

        $columns = [$this->getStatusColumn() => 0];

        $this->{$this->getStatusColumn()} = 0;

        if ($this->timestamps) {
            if ($this->usesDeleted()) {
                if (!is_null($this->getDeletedAtColumn())) {
                    $this->{$this->getDeletedAtColumn()} = $time;
                    $columns[$this->getDeletedAtColumn()] = $this->fromDateTime($time);
                }
            }
            if (!is_null($this->getUpdatedAtColumn())) {
                $this->{$this->getUpdatedAtColumn()} = $time;

                $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
            }
        }
        $query->update($columns);
        $this->syncOriginalAttributes(array_keys($columns));
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return null|bool
     */
    public function restore() {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getStatusColumn()} = 1;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed() {
        return $this->{$this->getStatusColumn()} == 0;
    }

    /**
     * Register a restoring model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function restoring($callback) {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a restored model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function restored($callback) {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Determine if the model is currently force deleting.
     *
     * @return bool
     */
    public function isForceDeleting() {
        return $this->forceDeleting;
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getStatusColumn() {
        /** @var CModel $this */
        return defined('static::STATUS') ? static::STATUS : 'status';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedStatusColumn() {
        return $this->getTable() . '.' . $this->getStatusColumn();
    }
}
