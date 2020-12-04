<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CConsole {

    const SUCCESS_EXIT = 0;
    const FAILURE_EXIT = 1;
    const EXCEPTION_EXIT = 2;

    public static function domain() {
        return CF::cliDomain();
    }

    public static function domainRequired($console) {
        $domain = CConsole::domain();
        if (strlen($domain) == 0) {
            $console->error('Domain is not set');
            $console->error('Please set your domain using domain:switch command');
            exit(static::FAILURE_EXIT);
        }
        if (!CF::domainExists($domain)) {
            $console->error('Domain '.$domain.' is not exists on data file');
            $console->error('Please recheck your domain or create the domain using domain:create command');
            exit(static::FAILURE_EXIT);
            
        }
    }

    public static function devSuiteRequired($console) {
        if (!CDevSuite::isInstalled()) {
            $console->error('devsuite is not installed');
            $console->error('Please install devsuite using devsuite:install command');
            exit(static::FAILURE_EXIT);
        }
    }

}
