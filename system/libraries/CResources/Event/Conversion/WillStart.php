<?php

class CResources_Event_Conversion_WillStart {
    use CQueue_Trait_SerializesModels;

    /** @var CApp_Model_Interface_ResourceInterface */
    public $resource;

    /** @var CResources_Conversion */
    public $conversion;

    /** @var string */
    public $copiedOriginalFile;

    public function __construct(CApp_Model_Interface_ResourceInterface $resource, CResources_Conversion $conversion, $copiedOriginalFile) {
        $this->resource = $resource;
        $this->conversion = $conversion;
        $this->copiedOriginalFile = $copiedOriginalFile;
    }
}
