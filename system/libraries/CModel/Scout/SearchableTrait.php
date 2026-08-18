<?php

trait CModel_Scout_SearchableTrait {
    /**
     * Additional metadata attributes managed by Scout.
     *
     * @var array
     */
    protected $scoutMetadata = [];

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootSearchableTrait() {
        static::addGlobalScope(new CModel_Scout_SearchableScope());

        static::observe(new CModel_Scout_ModelObserver());

        (new static())->registerSearchableMacros();
    }

    /**
     * Register the searchable macros.
     *
     * @return void
     */
    public function registerSearchableMacros() {
        $self = $this;

        CCollection::macro('searchable', function () use ($self) {
            $self->queueMakeSearchable($this);
        });

        CCollection::macro('unsearchable', function () use ($self) {
            $self->queueRemoveFromSearch($this);
        });
        CCollection::macro('searchableSync', function () use ($self) {
            $self->syncMakeSearchable($this);
        });

        CCollection::macro('unsearchableSync', function () use ($self) {
            $self->syncRemoveFromSearch($this);
        });
    }

    /**
     * Dispatch the job to make the given models searchable.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function queueMakeSearchable($models) {
        if ($models->isEmpty()) {
            return;
        }

        if (!CF::config('model.scout.queue')) {
            return $this->syncMakeSearchable($models);
        }

        c::dispatch((new CModel_Scout::$makeSearchableJob($models))
            ->onQueue($models->first()->syncWithSearchUsingQueue())
            ->onConnection($models->first()->syncWithSearchUsing()));
    }

    /**
     * Synchronously make the given models searchable.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function syncMakeSearchable($models) {
        if ($models->isEmpty()) {
            return;
        }

        return $models->first()->makeSearchableUsing($models)->first()->searchableUsing()->update($models);
    }

    /**
     * Dispatch the job to make the given models unsearchable.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function queueRemoveFromSearch($models) {
        if ($models->isEmpty()) {
            return;
        }

        if (!CF::config('model.scout.queue')) {
            return $this->syncRemoveFromSearch($models);
        }

        c::dispatch(new CModel_Scout::$removeFromSearchJob($models))
            ->onQueue($models->first()->syncWithSearchUsingQueue())
            ->onConnection($models->first()->syncWithSearchUsing());
    }

    /**
     * Synchronously make the given models unsearchable.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function syncRemoveFromSearch($models) {
        if ($models->isEmpty()) {
            return;
        }

        return $models->first()->searchableUsing()->delete($models);
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable() {
        return true;
    }

    /**
     * When updating a model, this method determines if we should update the search index.
     *
     * @return bool
     */
    public function searchIndexShouldBeUpdated() {
        return true;
    }

    /**
     * Perform a search against the model's indexed data.
     *
     * @param string   $query
     * @param \Closure $callback
     *
     * @return \CModel_Scout_Builder
     */
    public static function search($query = '', $callback = null) {
        return CContainer::getInstance()->make(static::$scoutBuilder ?? CModel_Scout_Builder::class, [
            'model' => new static(),
            'query' => $query,
            'callback' => $callback,
            'softDelete' => static::usesSoftDelete() && CF::config('model.scout.soft_delete', true),
        ]);
    }

    /**
     * Make all instances of the model searchable.
     *
     * @param int $chunk
     *
     * @return void
     */
    public static function makeAllSearchable($chunk = null) {
        static::makeAllSearchableQuery()->searchable($chunk);
    }

    /**
     * Get a query builder for making all instances of the model searchable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function makeAllSearchableQuery() {
        $self = new static();

        $softDelete = static::usesSoftDelete() && CF::config('model.scout.soft_delete', true);

        return $self->newQuery()
            ->when(true, function ($query) use ($self) {
                $self->makeAllSearchableUsing($query);
            })
            ->when($softDelete, function ($query) {
                $query->withTrashed();
            })
            ->orderBy(
                $self->qualifyColumn($self->getScoutKeyName())
            );
    }

    /**
     * Modify the collection of models being made searchable.
     *
     * @param \CCollection $models
     *
     * @return \CCollection
     */
    public function makeSearchableUsing(CCollection $models) {
        return $models;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param \CModel_Query $query
     *
     * @return \CModel_Query
     */
    protected function makeAllSearchableUsing($query) {
        return $query;
    }

    /**
     * Make the given model instance searchable.
     *
     * @return void
     */
    public function searchable() {
        $this->newCollection([$this])->searchable();
    }

    /**
     * Synchronously make the given model instance searchable.
     *
     * @return void
     */
    public function searchableSync() {
        $this->newCollection([$this])->searchableSync();
    }

    /**
     * Remove all instances of the model from the search index.
     *
     * @return void
     */
    public static function removeAllFromSearch() {
        $self = new static();

        $self->searchableUsing()->flush($self);
    }

    /**
     * Remove the given model instance from the search index.
     *
     * @return void
     */
    public function unsearchable() {
        $this->newCollection([$this])->unsearchable();
    }

    /**
     * Synchronously remove the given model instance from the search index.
     *
     * @return void
     */
    public function unsearchableSync() {
        $this->newCollection([$this])->unsearchableSync();
    }

    /**
     * Determine if the model existed in the search index prior to an update.
     *
     * @return bool
     */
    public function wasSearchableBeforeUpdate() {
        return true;
    }

    /**
     * Determine if the model existed in the search index prior to deletion.
     *
     * @return bool
     */
    public function wasSearchableBeforeDelete() {
        return true;
    }

    /**
     * Get the requested models from an array of object IDs.
     *
     * @param \CModel_Scout_Builder $builder
     * @param array                 $ids
     *
     * @return mixed
     */
    public function getScoutModelsByIds(CModel_Scout_Builder $builder, array $ids) {
        return $this->queryScoutModelsByIds($builder, $ids)->get();
    }

    /**
     * Get a query builder for retrieving the requested models from an array of object IDs.
     *
     * @param \CModel_Scout_Builder $builder
     * @param array                 $ids
     *
     * @return mixed
     */
    public function queryScoutModelsByIds(CModel_Scout_Builder $builder, array $ids) {
        $query = static::usesSoftDelete()
            ? $this->withTrashed() : $this->newQuery();

        if ($builder->queryCallback) {
            call_user_func($builder->queryCallback, $query);
        }
        $whereIn = in_array($this->getScoutKeyType(), ['int', 'integer'])
        ? 'whereIntegerInRaw'
        : 'whereIn';

        return $query->{$whereIn}(
            $this->qualifyColumn($this->getScoutKeyName()),
            $ids
        );
    }

    /**
     * Enable search syncing for this model.
     *
     * @return void
     */
    public static function enableSearchSyncing() {
        CModel_Scout_ModelObserver::enableSyncingFor(get_called_class());
    }

    /**
     * Disable search syncing for this model.
     *
     * @return void
     */
    public static function disableSearchSyncing() {
        CModel_Scout_ModelObserver::disableSyncingFor(get_called_class());
    }

    /**
     * Temporarily disable search syncing for the given callback.
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public static function withoutSyncingToSearch($callback) {
        static::disableSearchSyncing();

        try {
            return $callback();
        } finally {
            static::enableSearchSyncing();
        }
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs() {
        return CF::config('model.scout.prefix') . $this->getTable();
    }

    /**
     * Get the index name for the model when indexing.
     *
     * @return string
     */
    public function indexableAs() {
        return $this->searchableAs();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray() {
        return $this->toArray();
    }

    /**
     * Get the Scout engine for the model.
     *
     * @return mixed
     */
    public function searchableUsing() {
        return CContainer::getInstance()->make(CModel_Scout_EngineManager::class)->engine();
    }

    /**
     * Get the queue connection that should be used when syncing.
     *
     * @return string
     */
    public function syncWithSearchUsing() {
        return CF::config('model.scout.queue.connection') ?: CF::config('queue.default');
    }

    /**
     * Get the queue that should be used with syncing.
     *
     * @return string
     */
    public function syncWithSearchUsingQueue() {
        return CF::config('model.scout.queue.queue');
    }

    /**
     * Sync the soft deleted status for this model into the metadata.
     *
     * @return $this
     */
    public function pushSoftDeleteMetadata() {
        return $this->withScoutMetadata('__soft_deleted', $this->trashed() ? 1 : 0);
    }

    /**
     * Get all Scout related metadata.
     *
     * @return array
     */
    public function scoutMetadata() {
        return $this->scoutMetadata;
    }

    /**
     * Set a Scout related metadata.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function withScoutMetadata($key, $value) {
        $this->scoutMetadata[$key] = $value;

        return $this;
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey() {
        return $this->getKey();
    }

    /**
     * Get the auto-incrementing key type for querying models.
     *
     * @return string
     */
    public function getScoutKeyType() {
        return $this->getKeyType();
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getScoutKeyName() {
        return $this->getKeyName();
    }

    /**
     * Determine if the current class should use soft deletes with searching.
     *
     * @return bool
     */
    public static function usesSoftDelete() {
        return in_array(CModel_SoftDelete_Scope::class, c::classUsesRecursive(get_called_class()));
    }
}
