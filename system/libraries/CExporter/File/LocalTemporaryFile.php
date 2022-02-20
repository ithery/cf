<?php

class CExporter_File_LocalTemporaryFile extends CExporter_File_TemporaryFile {
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath) {
        touch($filePath);

        $this->filePath = realpath($filePath);
    }

    /**
     * @return string
     */
    public function getLocalPath() {
        return $this->filePath;
    }

    /**
     * @return bool
     */
    public function exists() {
        return file_exists($this->filePath);
    }

    /**
     * @return bool
     */
    public function delete() {
        if (@unlink($this->filePath) || !$this->exists()) {
            return true;
        }

        return unlink($this->filePath);
    }

    /**
     * @return resource
     */
    public function readStream() {
        return fopen($this->getLocalPath(), 'rb+');
    }

    /**
     * @return string
     */
    public function contents() {
        return file_get_contents($this->filePath);
    }

    /**
     * @param @param string|resource $contents
     */
    public function put($contents) {
        file_put_contents($this->filePath, $contents);
    }
}
