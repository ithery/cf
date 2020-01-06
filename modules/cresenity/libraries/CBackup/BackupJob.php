<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Carbon\Carbon;

class CBackup_BackupJob {

    /** @var CBackup_FileSelection */
    protected $fileSelection;

    /** @var CCollection */
    protected $dbDumpers;

    /** @var CCollection */
    protected $backupDestinations;

    /** @var string */
    protected $filename;

    /** @var CTemporary_Directory */
    protected $temporaryDirectory;

    /** @var bool */
    protected $sendNotifications = true;

    public function __construct() {
        $this->dontBackupFilesystem();
        $this->dontBackupDatabases();
        $this->setDefaultFilename();
        $this->backupDestinations = new CCollection();
    }

    public function dontBackupFilesystem() {
        $this->fileSelection = CBackup_FileSelection::create();
        return $this;
    }

    public function onlyDbName(array $allowedDbNames) {
        $this->dbDumpers = $this->dbDumpers->filter(
                function (CBackup_Database_AbstractDumper $dbDumper, $connectionName) use ($allowedDbNames) {
            return in_array($connectionName, $allowedDbNames);
        });
        return $this;
    }

    public function dontBackupDatabases() {
        $this->dbDumpers = new CCollection();
        return $this;
    }

    public function disableNotifications() {
        $this->sendNotifications = false;
        return $this;
    }

    public function setDefaultFilename() {
        $this->filename = Carbon::now()->format('Y-m-d-H-i-s') . '.zip';
        return $this;
    }

    public function setFileSelection(CBackup_FileSelection $fileSelection) {
        $this->fileSelection = $fileSelection;
        return $this;
    }

    public function setDatabaseDumpers(CCollection $dbDumpers) {
        $this->dbDumpers = $dbDumpers;
        return $this;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function onlyBackupTo($diskName) {
        $this->backupDestinations = $this->backupDestinations->filter(function (CBackup_BackupDestination $backupDestination) use ($diskName) {
            return $backupDestination->diskName() === $diskName;
        });
        if (!count($this->backupDestinations)) {
            throw CBackup_Exception_InvalidBackupJobException::destinationDoesNotExist($diskName);
        }
        return $this;
    }

    public function setBackupDestinations(CCollection $backupDestinations) {
        $this->backupDestinations = $backupDestinations;
        return $this;
    }

    public function run() {

        $temporaryDirectoryFolder = CF::config('backup.backup.temporary_folder', 'backup');
        $subfolder = date('YmdHis') . cstr::random(8);
        $path = $temporaryDirectoryFolder . DS . $subfolder;
        $this->temporaryDirectory = CTemporary::createDirectory($path);

        try {
            if (!count($this->backupDestinations)) {
                throw CBackup_Exception_InvalidBackupJobException::noDestinationsSpecified();
            }
            $manifest = $this->createBackupManifest();
            if (!$manifest->count()) {
                throw CBackup_Exception_InvalidBackupJobException::noFilesToBeBackedUp();
            }
            $zipFile = $this->createZipContainingEveryFileInManifest($manifest);
            $this->copyToBackupDestinations($zipFile);
        } catch (Exception $exception) {
            CBackup::output()->error("Backup failed because {$exception->getMessage()}." . PHP_EOL . $exception->getTraceAsString());
            //$this->sendNotification(new BackupHasFailed($exception));
            $this->temporaryDirectory->delete();
            throw $exception;
        }
        $this->temporaryDirectory->delete();
    }

    protected function createBackupManifest() {
        $databaseDumps = $this->dumpDatabases();
        CBackup::output()->info('Determining files to backup...');
        $manifest = CBackup_Manifest::create($this->temporaryDirectory->getPath('manifest.txt'))
                ->addFiles($databaseDumps)
                ->addFiles($this->filesToBeBackedUp());
        //$this->sendNotification(new BackupManifestWasCreated($manifest));
        return $manifest;
    }

    public function filesToBeBackedUp() {
        $this->fileSelection->excludeFilesFrom($this->directoriesUsedByBackupJob());
        return $this->fileSelection->selectedFiles();
    }

    protected function directoriesUsedByBackupJob() {
        return $this->backupDestinations
                        ->filter(function (CBackup_BackupDestination $backupDestination) {
                            return $backupDestination->filesystemType() === 'local';
                        })
                        ->map(function (CBackup_BackupDestination $backupDestination) {
                            return $backupDestination->disk()->getDriver()->getAdapter()->applyPathPrefix('') . $backupDestination->backupName();
                        })
                        ->each(function ( $backupDestinationDirectory) {
                            $this->fileSelection->excludeFilesFrom($backupDestinationDirectory);
                        })
                        ->push($this->temporaryDirectory->path())
                        ->toArray();
    }

    protected function createZipContainingEveryFileInManifest(Manifest $manifest) {
        CBackup::output()->info("Zipping {$manifest->count()} files and directories...");
        $pathToZip = $this->temporaryDirectory->path(CF::config('backup.backup.destination.filename_prefix') . $this->filename);
        $zip = CBackup_Zip::createForManifest($manifest, $pathToZip);
        CBackup::output()->info("Created zip containing {$zip->count()} files and directories. Size is {$zip->humanReadableSize()}");
        //$this->sendNotification(new BackupZipWasCreated($pathToZip));
        return $pathToZip;
    }

    /**
     * Dumps the databases to the given directory.
     * Returns an array with paths to the dump files.
     *
     * @return array
     */
    protected function dumpDatabases() {
        return $this->dbDumpers->map(function (CBackup_Database_AbstractDumper $dbDumper, $key) {
                    CBackup::output()->info("Dumping database {$dbDumper->getDbName()}...");
                    $dbType = mb_strtolower(basename(str_replace('\\', '/', get_class($dbDumper))));
                    $dbName = $dbDumper->getDbName();
                    if ($dbDumper instanceof CBackup_Database_Dumper_SqliteDumper) {
                        $dbName = $key . '-database';
                    }
                    $fileName = "{$dbType}-{$dbName}.{$this->getExtension($dbDumper)}";
                    if (CF::config('backup.backup.gzip_database_dump')) {
                        $dbDumper->useCompressor(new CBackup_Compressor_GzipCompressor());
                        $fileName .= '.' . $dbDumper->getCompressorExtension();
                    }
                    if ($compressor = CF::config('backup.backup.database_dump_compressor')) {
                        $dbDumper->useCompressor(new $compressor());
                        $fileName .= '.' . $dbDumper->getCompressorExtension();
                    }
                    $temporaryFilePath = $this->temporaryDirectory->getPath('db-dumps' . DIRECTORY_SEPARATOR . $fileName);
                    $dbDumper->dumpToFile($temporaryFilePath);
                    return $temporaryFilePath;
                })->toArray();
    }

    protected function copyToBackupDestinations($path) {
        $this->backupDestinations->each(function (CBackup_BackupDestination $backupDestination) use ($path) {
            try {
                CBackup::output()->info("Copying zip to disk named {$backupDestination->diskName()}...");
                $backupDestination->write($path);
                CBackup::output()->info("Successfully copied zip to disk named {$backupDestination->diskName()}.");
                $this->sendNotification(new BackupWasSuccessful($backupDestination));
            } catch (Exception $exception) {
                CBackup::output()->error("Copying zip failed because: {$exception->getMessage()}.");
                //$this->sendNotification(new BackupHasFailed($exception, $backupDestination ?? null));
            }
        });
    }

    protected function sendNotification($notification) {
        if ($this->sendNotifications) {
            c::rescue(function () use ($notification) {
                CEvent::dispatch($notification);
            }, function () {
                CBackup::output()->error('Sending notification failed');
            });
        }
    }

    protected function getExtension(CBackup_Database_AbstractDumper $dbDumper) {
        return $dbDumper instanceof CBackup_Database_Dumper_MongoDbDumper ? 'archive' : 'sql';
    }

}
