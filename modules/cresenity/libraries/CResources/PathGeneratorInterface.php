<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 1:26:03 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CResources_PathGeneratorInterface {
    /*
     * Get the path for the given resource, relative to the root storage path.
     */

    public function getPath(CApp_Model_Interface_ResourceInterface $resource);
    /*
     * Get the path for conversions of the given resource, relative to the root storage path.
     */

    public function getPathForConversions(CApp_Model_Interface_ResourceInterface $resource);
    /*
     * Get the path for responsive images of the given resource, relative to the root storage path.
     */

    public function getPathForResponsiveImages(CApp_Model_Interface_ResourceInterface $resource);
}
