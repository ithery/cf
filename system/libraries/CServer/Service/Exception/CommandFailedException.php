<?php

use Symfony\Component\Process\Process;

class CServer_Service_Exception_CommandFailedException extends \RuntimeException {
    /**
     * @var Process
     */
    private $process;

    /**
     * @param Process $process
     */
    public function __construct(Process $process) {
        $this->process = $process;

        $message = sprintf(
            'Command "%s" failed with code %s, error returned: %s',
            $this->process->getCommandLine(),
            $this->process->getExitCode(),
            $this->process->getErrorOutput()
        );

        parent::__construct($message, $this->process->getExitCode());
    }

    /**
     * @return Process
     */
    public function getProcess() {
        return $this->process;
    }
}
