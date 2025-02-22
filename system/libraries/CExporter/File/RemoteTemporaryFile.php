<?php

class CExporter_File_RemoteTemporaryFile extends CExporter_File_TemporaryFile {
    /**
     * @var string
     */
    private $disk;

    /**
     * @var null|CExporter_Disk
     */
    private $diskInstance;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var CExporter_File_LocalTemporaryFile
     */
    private $localTemporaryFile;

    /**
     * @param string                       $disk
     * @param string                       $filename
     * @param CExporter_File_TemporaryFile $localTemporaryFile
     */
    public function __construct($disk, $filename, CExporter_File_LocalTemporaryFile $localTemporaryFile) {
        $this->disk = $disk;
        $this->filename = $filename;
        $this->localTemporaryFile = $localTemporaryFile;

        $this->disk()->touch($filename);
    }

    public function __sleep() {
        return ['disk', 'filename', 'localTemporaryFile'];
    }

    /**
     * @return string
     */
    public function getLocalPath() {
        return $this->localTemporaryFile->getLocalPath();
    }

    /**
     * @return bool
     */
    public function exists() {
        return $this->disk()->exists($this->filename);
    }

    /**
     * @return bool
     */
    public function deleteLocalCopy() {
        return $this->localTemporaryFile->delete();
    }

    /**
     * @return bool
     */
    public function delete() {
        $this->localTemporaryFile->delete();

        return $this->disk()->delete($this->filename);
    }

    /**
     * @return CExporter_File_TemporaryFile
     */
    public function sync() {
        if (!$this->localTemporaryFile->exists()) {
            touch($this->localTemporaryFile->getLocalPath());
        }

        $this->disk()->copy(
            $this,
            $this->localTemporaryFile->getLocalPath()
        );

        return $this;
    }

    /**
     * Store on remote disk.
     */
    public function updateRemote() {
        $this->disk()->copy(
            $this->localTemporaryFile,
            $this->filename
        );
    }

    /**
     * @return resource
     */
    public function readStream() {
        return $this->disk()->readStream($this->filename);
    }

    /**
     * @return string
     */
    public function contents() {
        return $this->disk()->get($this->filename);
    }

    /**
     * @param string|resource $contents
     */
    public function put($contents) {
        $this->disk()->put($this->filename, $contents);
    }

    /**
     * @return CExporter_Disk
     */
    public function disk() {
        return $this->diskInstance ?: $this->diskInstance = CExporter_Storage::instance()->disk($this->disk);
    }
}
