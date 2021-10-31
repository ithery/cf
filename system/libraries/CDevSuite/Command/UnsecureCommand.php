<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Command_UnsecureCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{domain} {--all}';
    }

    public function run(CConsole_Command $cfCommand) {
        $domain = $cfCommand->argument('domain');
        $all = $cfCommand->option('all');
        if ($all) {
            CDevSuite::site()->unsecureAll();
            return;
        }

        $url = ($domain ?: CDevSuite::site()->host(getcwd())) . '.' . CDevSuite::configuration()->read()['tld'];

        CDevSuite::site()->unsecure($url);

        CDevSuite::nginx()->restart();

        CDevSuite::info('The [' . $url . '] site will now serve traffic over HTTP.');
    }

}
