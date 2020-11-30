<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '';
    }

    public function linuxNeedSudoCommandList() {
        return ["uninstall", "install", "start", "stop", "secure", "unsecure", "use"];
    }

    public abstract function run(CConsole_Command $cfCommand);
}
