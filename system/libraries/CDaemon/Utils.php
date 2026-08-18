<?php
use Symfony\Component\Process\Process;

class CDaemon_Utils {
    public static function daemonIsRunningWithPid($pid, $serviceClass) {
        $command = 'ps x | grep "' . $pid . '" | grep "'
        . 'serviceClass=' . $serviceClass
        . '" | grep -v "grep"';

        $result = '';
        if (defined('CFCLI')) {
            $process = new Process($command);
            $process->run();
            $result = $process->getOutput();
        } else {
            $result = shell_exec($command);
        }

        return strlen(trim($result)) > 0;
    }

    public static function supervisorIsRunningWithPid($pid, $alias) {
        $command = 'ps x | grep "' . $pid . '" | grep "'
        . $alias
        . '" | grep -v "grep"';

        $result = '';
        if (defined('CFCLI')) {
            $process = new Process($command);
            $process->run();
            $result = $process->getOutput();
        } else {
            $result = shell_exec($command);
        }

        return strlen(trim($result)) > 0;
    }
}
