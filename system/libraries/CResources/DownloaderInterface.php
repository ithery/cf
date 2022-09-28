<?php

interface CResources_DownloaderInterface {
    /**
     * @param string $url
     *
     * @return string
     */
    public function getTempFile($url);
}
