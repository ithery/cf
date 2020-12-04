<?php

/**
 * Description of CommandLine
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;

class CDevSuite_Windows_CommandLine extends CDevSuite_CommandLine {

    /**
     * Run the given command and die if fails.
     *
     * @param string   $command
     * @param callable $onError
     *
     * @return string
     */
    public function runOrDie($command, callable $onError = null) {

        return $this->run($command, function ($code, $output) use ($onError) {
                    if ($onError) {
                        $onError($code, $output);
                    }

                    exit;
                });
    }

   
}
