<?php

/**
 * Description of CommandLine.
 *
 * @author Hery
 */
class CDevSuite_Windows_CommandLine extends CDevSuite_CommandLine {
    /**
     * Simple global function to run commands.
     *
     * @param string $command
     *
     * @return void
     */
    public function quietlyAsUser($command) {
        $this->quietly($command . ' > /dev/null 2>&1');
    }

    /**
     * Run the given command.
     *
     * @param string   $command
     * @param callable $onError
     *
     * @return ProcessOutput
     */
    public function runAsUser($command, callable $onError = null) {
        return $this->runCommand($command, $onError);
    }

    /**
     * Run the given command with PowerShell.
     *
     * @param string        $command
     * @param null|callable $onError
     *
     * @return ProcessOutput
     */
    public function powershell(string $command, callable $onError = null) {
        return $this->runCommand("powershell -command \"$command\"", $onError);
    }
}
