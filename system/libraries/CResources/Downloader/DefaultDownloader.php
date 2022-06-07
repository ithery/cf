<?php
class CResources_Downloader_DefaultDownloader implements CResources_DownloaderInterface {
    /**
     * @param string $url
     *
     * @return string
     */
    public function getTempFile($url) {
        if (!$stream = @fopen($url, 'r')) {
            throw CResources_Exception_FileCannotBeAdded_UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($temporaryFile, $stream);

        return $temporaryFile;
    }
}
