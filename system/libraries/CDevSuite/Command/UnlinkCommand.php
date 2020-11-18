<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Command_UnlinkCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name} {--secure}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');
        CDevSuite::info('The [' . CDevSuite::site()->unlink($name) . '] symbolic link has been removed.');
    }

}
