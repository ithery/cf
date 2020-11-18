<?php

/**
 * Description of CommandLine
 *
 * @author Hery
 */
abstract class CDevSuite_CommandLine {

    /**
     * Simple global function to run commands.
     *
     * @param string $command
     *
     * @return void
     */
    public function quietly($command) {
        $this->runCommand($command . ' > /dev/null 2>&1');
    }

    /**
     * Run the given command.
     *
     * @param  string  $command
     * @param  callable $onError
     * @return string
     */
    public function runCommand($command, callable $onError = null) {
        $onError = $onError ? : function () {
            
        };

        // Symfony's 4.x Process component has deprecated passing a command string
        // to the constructor, but older versions (which DevSuite's Composer
        // constraints allow) don't have the fromShellCommandLine method.
        if (method_exists(Process::class, 'fromShellCommandline')) {
            $process = Process::fromShellCommandline($command);
        } else {
            $process = new Process($command);
        }

        $processOutput = '';
        $process->setTimeout(null)->run(function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
        });

        if ($process->getExitCode() > 0) {
            $onError($process->getExitCode(), $processOutput);
        }

        return $processOutput;
    }

    /**
     * Pass the command to the command line and display the output.
     *
     * @param string $command
     * @return void
     */
    public function passthru($command) {
        passthru($command);
    }

    /**
     * Simple global function to run commands.
     *
     * @param  string  $command
     * @return void
     */
    public function quietlyAsUser($command) {
        $this->quietly('sudo -u "' . CDevSuite::user() . '" ' . $command . ' > /dev/null 2>&1');
    }

    /**
     * Run the given command as the non-root user.
     *
     * @param string   $command
     * @param callable $onError
     * @return string
     */
    public function run($command, callable $onError = null) {
        return $this->runCommand($command, $onError);
    }

    /**
     * Run the given command.
     *
     * @param  string  $command
     * @param  callable $onError
     * @return string
     */
    public function runAsUser($command, callable $onError = null) {
        return $this->runCommand('sudo -u "' . CDevSuite::user() . '" ' . $command, $onError);
    }

}
