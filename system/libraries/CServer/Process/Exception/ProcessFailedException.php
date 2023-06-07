<?php

use Symfony\Component\Console\Exception\RuntimeException;

class CServer_Process_Exception_ProcessFailedException extends RuntimeException {
    /**
     * The process result instance.
     *
     * @var \CServer_Process_Contract_ProcessResultInterface
     */
    public $result;

    /**
     * Create a new exception instance.
     *
     * @param \CServer_Process_Contract_ProcessResultInterface $result
     *
     * @return void
     */
    public function __construct(CServer_Process_Contract_ProcessResultInterface $result) {
        $this->result = $result;

        parent::__construct(
            sprintf('The process "%s" failed.', $result->command()),
            $result->exitCode() ?? 1,
        );
    }
}
