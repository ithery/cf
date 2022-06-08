<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 30, 2019, 5:39:26 AM
 */
interface CResources_LoaderInterface {
    /**
     * @return string path of current resource
     */
    public function getPath();

    /**
     * @return string url of current resource
     */
    public function getUrl();
}
