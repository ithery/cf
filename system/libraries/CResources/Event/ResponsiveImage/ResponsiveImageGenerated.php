<?php

class CResources_Event_ResponsiveImage_ResponsiveImageGenerated {
    use CQueue_Trait_SerializesModels;

    /**
     * @var CModel_Resource_ResourceInterface
     */
    public $resource;

    public function __construct(CModel_Resource_ResourceInterface $resource) {
        $this->resource = $resource;
    }
}
