<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CResources_PathGeneratorInterface {
    /**
     * Get the path for the given resource, relative to the root storage path.
     */
    public function getPath(CModel_Resource_ResourceInterface $resource);

    /**
     * Get the path for conversions of the given resource, relative to the root storage path.
     */
    public function getPathForConversions(CModel_Resource_ResourceInterface $resource);

    /**
     * Get the path for responsive images of the given resource, relative to the root storage path.
     */
    public function getPathForResponsiveImages(CModel_Resource_ResourceInterface $resource);
}
