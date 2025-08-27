<?php

class CResources_Event_CollectionHasBeenCleared {
    use CQueue_Trait_SerializesModels;

    /**
     * Model of has resource.
     *
     * @var CModel_HasResourceInterface
     */
    public $model;

    public string $collectionName;

    public function __construct(CModel_HasResourceInterface $model, $collectionName) {
        $this->model = $model;

        $this->collectionName = $collectionName;
    }
}
