<?php

use Symfony\Component\Process\Process;

class CBackup_Database_Exception_DumpFailedException extends Exception {
    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function processDidNotEndSuccessfully(Process $process) {
        $processOutput = static::formatProcessOutput($process);

        return new static("The dump process failed with a none successful exitcode.{$processOutput}");
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function dumpfileWasNotCreated(Process $process) {
        $processOutput = static::formatProcessOutput($process);

        return new static("The dumpfile could not be created.{$processOutput}");
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \CDatabase_Exception_DumpFailed
     */
    public static function dumpfileWasEmpty(Process $process) {
        $processOutput = static::formatProcessOutput($process);

        return new static("The created dumpfile is empty.{$processOutput}");
    }

    protected static function formatProcessOutput(Process $process) {
        $output = $process->getOutput() ?: '<no output>';
        $errorOutput = $process->getErrorOutput() ?: '<no output>';
        $exitCodeText = $process->getExitCodeText() ?: '<no exit text>';

        return <<<CONSOLE
            Exitcode
            ========
            {$process->getExitCode()}: {$exitCodeText}
            Output
            ======
            {$output}
            Error Output
            ============
            {$errorOutput}
            CONSOLE;
    }
}
