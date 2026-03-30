<?php

defined('SYSPATH') or die('No direct access allowed.');

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
