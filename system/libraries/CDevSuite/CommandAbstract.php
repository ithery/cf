<?php

abstract class CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '';
    }

    public function linuxNeedSudoCommandList() {
        return ['uninstall', 'install', 'start', 'stop', 'secure', 'unsecure', 'use'];
    }

    abstract public function run(CConsole_Command $cfCommand);
}
