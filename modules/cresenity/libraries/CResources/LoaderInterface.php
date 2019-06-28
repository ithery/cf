<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 30, 2019, 5:39:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
