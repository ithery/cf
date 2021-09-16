<?php

class CModel_Scout_Event_ModelsImported {
    /**
     * The model collection.
     *
     * @var \CModel_Collection
     */
    public $models;

    /**
     * Create a new event instance.
     *
     * @param \CModel_Collection $models
     *
     * @return void
     */
    public function __construct($models) {
        $this->models = $models;
    }
}
