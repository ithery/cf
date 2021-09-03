<?php

class CModel_Scout_TaskQueue_RemoveFromSearch implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_QueueableTrait, CQueue_Trait_SerializesModels;

    /**
     * The models to be made searchable.
     *
     * @var \CModel_Collection
     */
    public $models;

    /**
     * Create a new job instance.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function __construct($models) {
        $this->models = CModel_Scout_RemoveableScoutCollection::make($models);
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle() {
        if ($this->models->isNotEmpty()) {
            $this->models->first()->searchableUsing()->delete($this->models);
        }
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param \CModel_ModelIdentifier $value
     *
     * @return \CModel_Collection
     */
    protected function restoreCollection($value) {
        if (!$value->class || count($value->id) === 0) {
            return new CModel_Collection();
        }

        return new CModel_Collection(
            c::collect($value->id)->map(function ($id) use ($value) {
                return c::tap(new $value->class, function ($model) use ($id) {
                    $keyName = $this->getUnqualifiedScoutKeyName(
                        $model->getScoutKeyName()
                    );

                    $model->forceFill([$keyName => $id]);
                });
            })
        );
    }

    /**
     * Get the unqualified Scout key name.
     *
     * @param string $keyName
     *
     * @return string
     */
    protected function getUnqualifiedScoutKeyName($keyName) {
        return cstr::afterLast($keyName, '.');
    }
}
