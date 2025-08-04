<?php
class CResources_Downloader_DefaultDownloader implements CResources_DownloaderInterface {
    /**
     * @param string $url
     *
     * @return string
     */
    public function getTempFile($url) {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => CF::config('resource.media_downloader_ssl'), ,
                'verify_peer_name' => CF::config('resource.media_downloader_ssl'),
            ],
            'http' => [
                'header' => 'User-Agent: CF ResourceLibrary',
            ],
        ]);

        if (!$stream = @fopen($url, 'r', false, $context)) {
            throw CResources_Exception_FileCannotBeAdded_UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'resource-library');

        file_put_contents($temporaryFile, $stream);

        fclose($stream);

        return $temporaryFile;
    }
}
