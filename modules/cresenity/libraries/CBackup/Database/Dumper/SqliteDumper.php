<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Process\Process;

class CBackup_Database_Dumper_SqliteDumper extends CBackup_Database_AbstractDumper {

    /**
     * Dump the contents of the database to a given file.
     *
     * @param string $dumpFile
     *
     * @throws \Spatie\DbDumper\Exceptions\DumpFailed
     */
    public function dumpToFile($dumpFile) {
        $command = $this->getDumpCommand($dumpFile);
        $process = Process::fromShellCommandline($command, null, null, null, $this->timeout);
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
        $command = sprintf(
                "echo 'BEGIN IMMEDIATE;\n.dump' | '%ssqlite3' --bail '%s'", $this->dumpBinaryPath, $this->dbName
        );
        return $this->echoToFile($command, $dumpFile);
    }

}
