<?php

use Symfony\Component\Process\Process;

class CBackup_Database_Dumper_MongoDbDumper extends CBackup_Database_AbstractDumper {
    protected $port = 27017;

    /**
     * @var null|string
     */
    protected $collection = null;

    /**
     * @var null|string
     */
    protected $authenticationDatabase = null;

    /**
     * Dump the contents of the database to the given file.
     *
     * @param string $dumpFile
     *
     * @throws \CBackup_Database_Exception_CannotStartDumpException
     * @throws \CBackup_Database_Exception_DumpFailedException
     */
    public function dumpToFile($dumpFile) {
        $this->guardAgainstIncompleteCredentials();
        $command = $this->getDumpCommand($dumpFile);
        $process = Process::fromShellCommandline($command, null, null, null, $this->timeout);
        $process->run();
        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Verifies if the dbname and host options are set.
     *
     * @throws \CBackup_Database_Exception_CannotStartDumpException
     *
     * @return void
     */
    protected function guardAgainstIncompleteCredentials() {
        foreach (['dbName', 'host'] as $requiredProperty) {
            if (strlen($this->$requiredProperty) === 0) {
                throw CBackup_Database_Exception_CannotStartDumpException::emptyParameter($requiredProperty);
            }
        }
    }

    /**
     * @param string $collection
     *
     * @return CBackup_Database_Dumper_MongoDbDumper
     */
    public function setCollection($collection) {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param string $authenticationDatabase
     *
     * @return CBackup_Database_Dumper_MongoDbDumper
     */
    public function setAuthenticationDatabase($authenticationDatabase) {
        $this->authenticationDatabase = $authenticationDatabase;
        return $this;
    }

    /**
     * Generate the dump command for MongoDb.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getDumpCommand($filename) {
        $quote = $this->determineQuote();
        $command = [
            "{$quote}{$this->dumpBinaryPath}mongodump{$quote}",
            "--db {$this->dbName}",
            '--archive',
        ];
        if ($this->userName) {
            $command[] = "--username '{$this->userName}'";
        }
        if ($this->password) {
            $command[] = "--password '{$this->password}'";
        }
        if (isset($this->host)) {
            $command[] = "--host {$this->host}";
        }
        if (isset($this->port)) {
            $command[] = "--port {$this->port}";
        }
        if (isset($this->collection)) {
            $command[] = "--collection {$this->collection}";
        }
        if ($this->authenticationDatabase) {
            $command[] = "--authenticationDatabase {$this->authenticationDatabase}";
        }
        return $this->echoToFile(implode(' ', $command), $filename);
    }
}
