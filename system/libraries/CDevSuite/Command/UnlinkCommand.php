<?php

class CDevSuite_Command_UnlinkCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{name?} {--secure}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name') ?: CF::appCode();
        CDevSuite::info('The [' . CDevSuite::site()->unlink($name) . '] symbolic link has been removed.');
    }
}
