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
    protected $requiredFolders = [
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
            
            if(CServer::getOS()!=CServer::OS_WINNT) {
                if($file=='ngrok') {
                    $command = sprintf('chmod +x %s',$this->binPath($file));
                    CDevSuite::commandLine()->run($command);
                }
            }
        }
        foreach ($this->requiredFolders as $folder) {
            if(!$this->files->isDir($this->binPath($folder))) {
                $this->files->mkdir($this->binPath($folder));
            }
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

        $this->downloadChunked($url, $targetPath);
        //$this->files->put($targetPath, $this->getUrl($url));

        return true;
    }

    /**
     * Copy remote file over HTTP one small chunk at a time.
     *
     * @param $infile The full URL to the remote file
     * @param $outfile The path where to save the file
     */
    protected function downloadChunked($infile, $outfile) {
        $chunksize = 10 * (1024 * 1024); // 10 Megs

        /**
         * parse_url breaks a part a URL into it's parts, i.e. host, path,
         * query string, etc.
         */
        $parts = parse_url($infile);
        $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
        $o_handle = fopen($outfile, 'wb');

        if ($i_handle == false || $o_handle == false) {
            return false;
        }

        if (!empty($parts['query'])) {
            $parts['path'] .= '?' . $parts['query'];
        }

        /**
         * Send the request to the server for the file
         */
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        /**
         * Now read the headers from the remote server. We'll need
         * to get the content length.
         */
        $headers = array();
        while (!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n")
                break;
            $headers[] = $line;
        }

        /**
         * Look for the Content-Length header, and get the size
         * of the remote file.
         */
        $length = 0;
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int) str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        /**
         * Start reading in the remote file, and writing it to the
         * local file one chunk at a time.
         */
        $cnt = 0;
        CDevSuite::progressStart($length);
        while (!feof($i_handle)) {
            $buf = '';
            $buf = fread($i_handle, $chunksize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;
            CDevSuite::progressAdvance($bytes);

            /**
             * We're done reading when we've reached the conent length
             */
            if ($cnt >= $length)
                break;
        }
        CDevSuite::progressFinish();

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
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
