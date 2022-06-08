<?php
class CResources_TaskQueue_GenerateResponsiveImage extends CQueue_AbstractTask {
    /**
     * @var CModel_Resource_ResourceInterface
     */
    protected $resource;

    public function __construct(CModel_Resource_ResourceInterface $resource) {
        $this->resource = $resource;
    }

    public function handle() {
        /** @var \CResources_ResponsiveImage_Generator $responsiveImageGenerator */
        $responsiveImageGenerator = CResources_Factory::createResponsiveImageGenerator();

        $responsiveImageGenerator->generateResponsiveImages($this->resource);

        return true;
    }
}
