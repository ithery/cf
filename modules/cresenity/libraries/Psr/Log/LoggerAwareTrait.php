<?php

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait Psr_Log_LoggerAwareTrait {

    /**
     * The logger instance.
     *
     * @var Psr_Log_LoggerInterface
     */
    protected $logger;

    /**
     * Sets a logger.
     *
     * @param Psr_Log_LoggerInterface $logger
     */
    public function setLogger(Psr_Log_LoggerInterface $logger) {
        $this->logger = $logger;
    }

}
