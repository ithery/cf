<?php

class CModel_Scout_TaskQueue_MakeSearchable implements CQueue_ShouldQueueInterface {
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
        $this->models = $models;
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle() {
        if (count($this->models) === 0) {
            return;
        }

        $this->models->first()->searchableUsing()->update($this->models);
    }
}
