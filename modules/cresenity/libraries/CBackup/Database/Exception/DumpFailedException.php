<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Process\Process;

class CBackup_Database_Exception_DumpFailedException extends Exception {

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function processDidNotEndSuccessfully(Process $process) {
        return new static("The dump process failed with exitcode {$process->getExitCode()} : {$process->getExitCodeText()} : {$process->getErrorOutput()}");
    }

    /**
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function dumpfileWasNotCreated() {
        return new static('The dumpfile could not be created');
    }

    /**
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function dumpfileWasEmpty() {
        return new static('The created dumpfile is empty');
    }

}
