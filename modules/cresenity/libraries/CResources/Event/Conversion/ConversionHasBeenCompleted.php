<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Event_Conversion_ConversionHasBeenCompleted {

    use CQueue_Trait_SerializesModels;

    /** @var CApp_Model_Interface_ResourceInterface */
    public $resource;

    /** @var CResources_Conversion */
    public $conversion;

    public function __construct(CApp_Model_Interface_ResourceInterface $resource, CResources_Conversion $conversion) {
        $this->resource = $resource;
        $this->conversion = $conversion;
    }

}
