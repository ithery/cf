<?php

/**
 * Describes a logger-aware instance.
 */
interface Psr_Log_LoggerAwareInterface {

    /**
     * Sets a logger instance on the object.
     *
     * @param Psr_Log_LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(Psr_Log_LoggerInterface $logger);
}
