<?php

/**
 * Description of DevCloud
 *
 * @author Hery
 */
class CDevSuite_DevCloud {

    protected $baseDownloadUrl;
    protected $baseBinPath;

    public function __construct() {
        $downloadUrl = 'https://cpanel.ittron.co.id/application/devcloud/default/data/bin/' . CDevSuite::osFolder() . '/';
        $this->baseDownloadUrl = $downloadUrl;
        $this->baseBinPath = CDevSuite::binPath();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Download from devcloud
     *
     */
    public function download($file) {
        $url = $this->baseDownloadUrl . $file;


        $targetPath = $this->baseBinPath . $file;
        $targetDir = dirname($targetPath);
        $this->files->ensureDirExists($targetDir);
        $this->files->put($targetPath, $this->getUrl($url));

        return true;
    }

    /**
     * Download from devcloud
     *
     */
    public function downloadIfNotExists($file) {


        $targetPath = $this->baseBinPath . $file;
        if (!file_exists($targetPath)) {
            return $this->download($file);
        }
        return false;
    }

    /**
     * Get the contents of a URL using the 'proxy' and 'ssl-no-verify' command options.
     *
     * @param  string  $url
     * @return string|bool
     */
    protected function getUrl($url) {
        $contextOptions = [];

        /*
          if ($this->option('proxy')) {
          $contextOptions['http'] = [
          'proxy' => $this->option('proxy'),
          'request_fulluri' => true
          ];
          }
         */
        $contextOptions['ssl'] = ['verify_peer' => false];

        $streamContext = stream_context_create($contextOptions);

        return file_get_contents($url, false, $streamContext);
    }

}
