<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_QueuedWriter {
    /**
     * @var CExporter_QueuedWriter
     */
    protected static $instance;

    /**
     * @var CExporter_Writer
     */
    protected $writer;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var CExporter_File_TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    private function __construct() {
        $this->writer = CExporter_Writer::instance();
        $this->chunkSize = CExporter::config()->get('exports.chunk_size', 1000);
        $this->temporaryFileFactory = CExporter_File_TemporaryFileFactory::instance();
    }

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CExporter_QueuedWriter();
        }
        return static::$instance;
    }

    /**
     * @param object       $export
     * @param string       $filePath
     * @param string       $disk
     * @param string|null  $writerType
     * @param array|string $diskOptions
     *
     * @return CQueue_PendingDispatch
     */
    public function store($export, $filePath, $disk = null, $writerType = null, $diskOptions = []) {
        $temporaryFile = $this->temporaryFileFactory->make();

        $jobs = $this->buildExportJobs($export, $temporaryFile, $writerType);

        $jobs->push(new CExporter_TaskQueue_StoreQueuedExport(
            $temporaryFile,
            $filePath,
            $disk,
            $diskOptions
        ));

        return CExporter_TaskQueue_QueueExport::withChain($jobs->toArray())->dispatch($export, $temporaryFile, $writerType);
    }

    /**
     * @param object                       $export
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     *
     * @return CCollection
     */
    private function buildExportJobs($export, CExporter_File_TemporaryFile $temporaryFile, $writerType) {
        $sheetExports = [$export];
        if ($export instanceof CExporter_Concern_WithMultipleSheets) {
            $sheetExports = $export->sheets();
        }

        $jobs = new CCollection;
        foreach ($sheetExports as $sheetIndex => $sheetExport) {
            if ($sheetExport instanceof CExporter_Concern_FromCollection) {
                $jobs = $jobs->merge($this->exportCollection($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof CExporter_Concern_FromQuery) {
                $jobs = $jobs->merge($this->exportQuery($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof CExporter_Concern_FromView) {
                $jobs = $jobs->merge($this->exportView($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            }

            $jobs->push(new CExporter_TaskQueue_CloseSheet($sheetExport, $temporaryFile, $writerType, $sheetIndex));
        }

        return $jobs;
    }

    /**
     * @param CExporter_Concern_FromCollection $export
     * @param CExporter_File_TemporaryFile     $temporaryFile
     * @param string                           $writerType
     * @param int                              $sheetIndex
     *
     * @return CCollection
     */
    private function exportCollection(CExporter_Concern_FromCollection $export, CExporter_File_TemporaryFile $temporaryFile, $writerType, $sheetIndex) {
        return $export
            ->collection()
            ->chunk($this->getChunkSize($export))
            ->map(function ($rows) use ($writerType, $temporaryFile, $sheetIndex, $export) {
                if ($rows instanceof Traversable) {
                    $rows = iterator_to_array($rows);
                }

                return new CExporter_TaskQueue_AppendDataToSheet(
                    $export,
                    $temporaryFile,
                    $writerType,
                    $sheetIndex,
                    $rows
                );
            });
    }

    /**
     * @param CExporter_Concern_FromQuery  $export
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     * @param int                          $sheetIndex
     *
     * @return CCollection
     */
    private function exportQuery(CExporter_Concern_FromQuery $export, CExporter_File_TemporaryFile $temporaryFile, $writerType, $sheetIndex) {
        $query = $export->query();

        $count = $export instanceof CExporter_Concern_WithCustomQuerySize ? $export->querySize() : $query->count();
        $spins = ceil($count / $this->getChunkSize($export));

        $jobs = new CCollection();

        for ($page = 1; $page <= $spins; $page++) {
            $jobs->push(new CExporter_TaskQueue_AppendQueryToSheet(
                $export,
                $temporaryFile,
                $writerType,
                $sheetIndex,
                $page,
                $this->getChunkSize($export)
            ));
        }

        return $jobs;
    }

    /**
     * @param CExporter_Concern_FromView   $export
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     * @param int                          $sheetIndex
     *
     * @return CCollection
     */
    private function exportView(CExporter_Concern_FromView $export, TemporarCExporter_File_TemporaryFileyFile $temporaryFile, $writerType, $sheetIndex) {
        $jobs = new CCollection();
        $jobs->push(new CExporter_TaskQueue_AppendViewToSheet(
            $export,
            $temporaryFile,
            $writerType,
            $sheetIndex
        ));

        return $jobs;
    }

    /**
     * @param object|CExporter_Concern_WithCustomChunkSize $export
     *
     * @return int
     */
    private function getChunkSize($export) {
        if ($export instanceof CExporter_Concern_WithCustomChunkSize) {
            return $export->chunkSize();
        }

        return $this->chunkSize;
    }
}
