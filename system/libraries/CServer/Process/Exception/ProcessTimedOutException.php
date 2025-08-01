<?php

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;

class CServer_Process_Exception_ProcessTimedOutException extends RuntimeException {
    /**
     * The process result instance.
     *
     * @var \CServer_Process_Contract_ProcessResultInterface
     */
    public $result;

    /**
     * Create a new exception instance.
     *
     * @param \Symfony\Component\Process\Exception\ProcessTimedOutException $original
     * @param \CServer_Process_Contract_ProcessResultInterface              $result
     *
     * @return void
     */
    public function __construct(SymfonyTimeoutException $original, CServer_Process_Contract_ProcessResultInterface $result) {
        $this->result = $result;

        parent::__construct($original->getMessage(), $original->getCode(), $original);
    }
}
