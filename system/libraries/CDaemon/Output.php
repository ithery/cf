<?php

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class CDaemon_Output extends Output implements OutputInterface {
    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool   $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline) {
        return CDaemon::log($message . ($newline ? PHP_EOL : ''));
    }
}
