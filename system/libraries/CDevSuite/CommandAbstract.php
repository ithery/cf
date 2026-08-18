<?php

/**
 * Description of CommandAbstract.
 */
abstract class CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '';
    }

    /**
     * Get the list of command names that need sudo on Linux.
     *
     * @return array
     */
    public function linuxNeedSudoCommandList() {
        return ['uninstall', 'install', 'start', 'stop', 'secure', 'unsecure', 'use'];
    }

    /**
     * Run the command.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return mixed
     */
    abstract public function run(CConsole_Command $cfCommand);
}
