<?php

/**
 * Description of DevCloud
 *
 * @author Hery
 */
abstract class CDevSuite_DevCloud {

    protected $baseDownloadUrl;
    protected $baseBinPath;
    protected $requiredFiles = [
    ];

    /**
     *
     * @var CDevSuite_Filesystem
     */
    protected $files;

    public function __construct() {
        $downloadUrl = 'http://cpanel.ittron.co.id/application/devcloud/default/data/bin/devsuite/' . CDevSuite::osFolder() . '/';
        $this->baseDownloadUrl = $downloadUrl;
        $this->baseBinPath = CDevSuite::binPath();
        $this->files = CDevSuite::filesystem();
    }

    public function install() {
        foreach ($this->requiredFiles as $file) {
            $this->downloadIfNotExists($file);
        }
    }

    public function uninstall() {
        foreach ($this->requiredFiles as $file) {
            $this->binDelete($file);
        }
    }

    /**
     * Download from devcloud
     *
     */
    public function download($file) {

        CDevSuite::info('Downloading ' . $file . '');
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

    public function binPath($file) {
        return $this->baseBinPath . $file;
    }

    public function binExists($file) {
        return $this->files->exists($this->binPath($file));
    }

    public function binDelete($file) {
        CDevSuite::info('Deleting ' . $file);
        return $this->files->unlink($this->binPath($file));
    }

}
