<?php

/**
 * Description of FileSessionHandler
 *
 * @author Hery
 */

use Symfony\Component\Finder\Finder;

class CSession_Handler_FileSessionHandler implements SessionHandlerInterface {

    /**
     * The path where sessions should be stored.
     *
     * @var string
     */
    protected $path;

    /**
     * The number of minutes the session should be valid.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new file driven handler instance.
     *
     * @param  string  $path
     * @param  int  $minutes
     * @return void
     */
    public function __construct($path, $minutes) {
        $this->files = new CFile();
        $this->path = DOCROOT.'temp'.DS.'session';
        if(!is_dir($this->path)) {
            CFile::makeDirectory($this->path, 0755,true);
        }
        $this->minutes = $minutes;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId) {
        if ($this->files->isFile($path = $this->path . '/' . $sessionId)) {
            if ($this->files->lastModified($path) >= CCarbon::now()->subMinutes($this->minutes)->getTimestamp()) {
                return $this->files->sharedGet($path);
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data) {

        $this->files->put($this->path . '/' . $sessionId, $data, true);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId) {
        $this->files->delete($this->path . '/' . $sessionId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime) {
        $files = Finder::create()
                ->in($this->path)
                ->files()
                ->ignoreDotFiles(true)
                ->date('<= now - ' . $lifetime . ' seconds');

        foreach ($files as $file) {
            $this->files->delete($file->getRealPath());
        }
    }

}
