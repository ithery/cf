<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_TaskQueue_StoreQueuedExport implements CQueue_AbstractTask {

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string|null
     */
    private $disk;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var array|string
     */
    private $diskOptions;

    /**
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string        $filePath
     * @param string|null   $disk
     * @param array|string  $diskOptions
     */
    public function __construct(CExporter_File_TemporaryFile $temporaryFile, $filePath, $disk = null, $diskOptions = []) {
        $this->disk = $disk;
        $this->filePath = $filePath;
        $this->temporaryFile = $temporaryFile;
        $this->diskOptions = $diskOptions;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function execute(CExporter_Storage $filesystem) {
        $filesystem->disk($this->disk, $this->diskOptions)->copy(
                $this->temporaryFile, $this->filePath
        );

        $this->temporaryFile->delete();
    }

}
