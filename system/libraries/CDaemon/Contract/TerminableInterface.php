<?php
interface CDaemon_Contract_TerminableInterface {
    /**
     * Terminate the process.
     *
     * @param int $status
     *
     * @return void
     */
    public function terminate($status = 0);
}
