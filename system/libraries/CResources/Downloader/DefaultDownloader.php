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
                'verify_peer' => CF::config('resource.resource_downloader_ssl'),
                'verify_peer_name' => CF::config('resource.resource_downloader_ssl'),
            ],
            'http' => [
                'header' => 'User-Agent: CF ResourceLibrary',
            ],
        ]);

        $retryTimes = (int) CF::config('resource.resource_downloader_retry_times', 3, false);
        $retryDelay = (int) CF::config('resource.resource_downloader_retry_delay', 2, false);

        $stream = false;
        for ($attempt = 0; $attempt <= $retryTimes; $attempt++) {
            $stream = @fopen($url, 'r', false, $context);
            if ($stream !== false) {
                break;
            }
            if ($attempt < $retryTimes) {
                sleep($retryDelay);
            }
        }

        if ($stream === false) {
            throw CResources_Exception_FileCannotBeAdded_UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'resource-library');

        file_put_contents($temporaryFile, $stream);

        fclose($stream);

        return $temporaryFile;
    }
}
