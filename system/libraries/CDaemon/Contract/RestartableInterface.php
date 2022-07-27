<?php

interface CDaemon_Contract_RestartableInterface {
    /**
     * Restart the process.
     *
     * @return void
     */
    public function restart();
}
