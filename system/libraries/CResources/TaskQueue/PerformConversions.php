<?php

class CResources_TaskQueue_PerformConversions extends CQueue_AbstractTask {
    /**
     * @var CResources_ConversionCollection
     */
    protected $conversions;

    /**
     * @var CModel_Resource_ResourceInterface
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $onlyMissing;

    public function __construct(CResources_ConversionCollection $conversions, CModel_Resource_ResourceInterface $resource, $onlyMissing = false) {
        $this->conversions = $conversions;
        $this->resource = $resource;
        $this->onlyMissing = $onlyMissing;
    }

    public function execute() {
        CResources_Factory::createFileManipulator()->performConversions($this->conversions, $this->resource, $this->onlyMissing);

        return true;
    }
}
