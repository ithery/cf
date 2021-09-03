<?php

class CModel_Scout_ModelObserver {
    /**
     * Indicates if Scout will dispatch the observer's events after all database transactions have committed.
     *
     * @var bool
     */
    public $afterCommit;

    /**
     * Indicates if Scout will keep soft deleted records in the search indexes.
     *
     * @var bool
     */
    protected $usingSoftDeletes;

    /**
     * Indicates if the model is currently force saving.
     *
     * @var bool
     */
    protected $forceSaving = false;

    /**
     * The class names that syncing is disabled for.
     *
     * @var array
     */
    protected static $syncingDisabledFor = [];

    /**
     * Create a new observer instance.
     *
     * @return void
     */
    public function __construct() {
        $this->afterCommit = CF::config('model.scout.after_commit', false);
        $this->usingSoftDeletes = CF::config('model.scout.soft_delete', true);
    }

    /**
     * Enable syncing for the given class.
     *
     * @param string $class
     *
     * @return void
     */
    public static function enableSyncingFor($class) {
        unset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Disable syncing for the given class.
     *
     * @param string $class
     *
     * @return void
     */
    public static function disableSyncingFor($class) {
        static::$syncingDisabledFor[$class] = true;
    }

    /**
     * Determine if syncing is disabled for the given class or model.
     *
     * @param object|string $class
     *
     * @return bool
     */
    public static function syncingDisabledFor($class) {
        $class = is_object($class) ? get_class($class) : $class;

        return isset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Handle the saved event for the model.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function saved($model) {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if (!$this->forceSaving && !$model->searchIndexShouldBeUpdated()) {
            return;
        }

        if (!$model->shouldBeSearchable()) {
            if ($model->wasSearchableBeforeUpdate()) {
                $model->unsearchable();
            }

            return;
        }

        $model->searchable();
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function deleted($model) {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if (!$model->wasSearchableBeforeDelete()) {
            return;
        }

        if ($this->usingSoftDeletes && $this->usesSoftDelete($model)) {
            $this->whileForcingUpdate(function () use ($model) {
                $this->saved($model);
            });
        } else {
            $model->unsearchable();
        }
    }

    /**
     * Handle the force deleted event for the model.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function forceDeleted($model) {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        $model->unsearchable();
    }

    /**
     * Handle the restored event for the model.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function restored($model) {
        $this->whileForcingUpdate(function () use ($model) {
            $this->saved($model);
        });
    }

    /**
     * Execute the given callback while forcing updates.
     *
     * @param \Closure $callback
     *
     * @return mixed
     */
    protected function whileForcingUpdate(Closure $callback) {
        $this->forceSaving = true;

        $result = $callback();

        $this->forceSaving = false;

        return $result;
    }

    /**
     * Determine if the given model uses soft deletes.
     *
     * @param \CModel $model
     *
     * @return bool
     */
    protected function usesSoftDelete($model) {
        return in_array(CModel_SoftDelete_SoftDeleteTrait::class, c::classUsesRecursive($model));
    }
}
