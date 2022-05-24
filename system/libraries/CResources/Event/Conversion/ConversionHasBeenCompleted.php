<?php

class CResources_Event_Conversion_ConversionHasBeenCompleted {
    use CQueue_Trait_SerializesModels;

    /**
     * @var CModel_Resource_ResourceInterface
     */
    public $resource;

    /**
     * @var CResources_Conversion
     */
    public $conversion;

    public function __construct(CModel_Resource_ResourceInterface $resource, CResources_Conversion $conversion) {
        $this->resource = $resource;
        $this->conversion = $conversion;
    }
}
