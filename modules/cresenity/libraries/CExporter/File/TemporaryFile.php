<?php

abstract class CExporter_File_TemporaryFile {
    /**
     * @return string
     */
    abstract public function getLocalPath();

    /**
     * @return bool
     */
    abstract public function exists();

    /**
     * @param @param string|resource $contents
     */
    abstract public function put($contents);

    /**
     * @return bool
     */
    abstract public function delete();

    /**
     * @return resource
     */
    abstract public function readStream();

    /**
     * @return string
     */
    abstract public function contents();

    /**
     * @return CExporter_File_TemporaryFile
     */
    public function sync() {
        return $this;
    }

    /**
     * @param string|UploadedFile $filePath
     * @param string|null         $disk
     *
     * @return CExporter_File_TemporaryFile
     */
    public function copyFrom($filePath, $disk = null) {
        if ($filePath instanceof CHTTP_UploadedFile) {
            $readStream = fopen($filePath->getRealPath(), 'rb');
        } elseif ($disk === null && realpath($filePath) !== false) {
            $readStream = fopen($filePath, 'rb');
        } else {
            $readStream = CStorage::instance()->disk($disk)->readStream($filePath);
        }

        $this->put($readStream);

        if (is_resource($readStream)) {
            fclose($readStream);
        }

        return $this->sync();
    }
}
