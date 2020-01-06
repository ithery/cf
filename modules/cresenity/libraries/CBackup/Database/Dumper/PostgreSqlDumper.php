<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Process\Process;

class CBackup_Database_Dumper_PostgreSqlDumper extends CBackup_Database_AbstractDumper {

    /** @var bool */
    protected $useInserts = false;

    /** @var bool */
    protected $createTables = true;

    public function __construct() {
        $this->port = 5432;
    }

    /**
     * @return $this
     */
    public function useInserts() {
        $this->useInserts = true;
        return $this;
    }

    /**
     * Dump the contents of the database to the given file.
     *
     * @param string $dumpFile
     *
     * @throws \Spatie\DbDumper\Exceptions\CannotStartDump
     * @throws \Spatie\DbDumper\Exceptions\DumpFailed
     */
    public function dumpToFile($dumpFile) {
        $this->guardAgainstIncompleteCredentials();
        $command = $this->getDumpCommand($dumpFile);
        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getContentsOfCredentialsFile());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];
        $envVars = $this->getEnvironmentVariablesForDumpCommand($temporaryCredentialsFile);
        $process = Process::fromShellCommandline($command, null, $envVars, null, $this->timeout);
        $process->run();
        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Get the command that should be performed to dump the database.
     *
     * @param string $dumpFile
     *
     * @return string
     */
    public function getDumpCommand($dumpFile) {
        $quote = $this->determineQuote();
        $command = [
            "{$quote}{$this->dumpBinaryPath}pg_dump{$quote}",
            "-U {$this->userName}",
            '-h ' . ($this->socket === '' ? $this->host : $this->socket),
            "-p {$this->port}",
        ];
        if ($this->useInserts) {
            $command[] = '--inserts';
        }
        if (!$this->createTables) {
            $command[] = '--data-only';
        }
        foreach ($this->extraOptions as $extraOption) {
            $command[] = $extraOption;
        }
        if (!empty($this->includeTables)) {
            $command[] = '-t ' . implode(' -t ', $this->includeTables);
        }
        if (!empty($this->excludeTables)) {
            $command[] = '-T ' . implode(' -T ', $this->excludeTables);
        }
        return $this->echoToFile(implode(' ', $command), $dumpFile);
    }

    public function getContentsOfCredentialsFile() {
        $contents = [
            $this->host,
            $this->port,
            $this->dbName,
            $this->userName,
            $this->password,
        ];
        return implode(':', $contents);
    }

    protected function guardAgainstIncompleteCredentials() {
        foreach (['userName', 'dbName', 'host'] as $requiredProperty) {
            if (empty($this->$requiredProperty)) {
                throw CBackup_Database_Exception_CannotStartDumpException::emptyParameter($requiredProperty);
            }
        }
    }

    protected function getEnvironmentVariablesForDumpCommand($temporaryCredentialsFile) {
        return [
            'PGPASSFILE' => $temporaryCredentialsFile,
            'PGDATABASE' => $this->dbName,
        ];
    }

    /**
     * @return $this
     */
    public function doNotCreateTables() {
        $this->createTables = false;
        return $this;
    }

}
