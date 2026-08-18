<?php
require_once DOCROOT . 'system/vendor/Termwind/Functions.php';

class CConsole {
    const SUCCESS_EXIT = 0;

    const FAILURE_EXIT = 1;

    const EXCEPTION_EXIT = 2;

    /**
     * CConsole Kernel.
     *
     * @var CConsole_Kernel
     */
    protected static $kernel;

    public static function domain() {
        return CF::cliDomain();
    }

    public static function appCode() {
        return CF::cliAppCode();
    }

    public static function prefix() {
        $appConfig = CConfig::instance('app');
        // $appConfig->refresh();

        return $appConfig->get('prefix');
    }

    public static function domainRequired($console) {
        $domain = CConsole::domain();
        if (strlen($domain) == 0) {
            $console->error('Domain is not set');
            $console->error('Please set your domain using domain:switch command');
            exit(static::FAILURE_EXIT);
        }
        if (!CF::domainExists($domain)) {
            $console->error('Domain ' . $domain . ' is not exists on data file');
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

    public static function phpUnitRequired($console) {
        if (!CTesting::isInstalled()) {
            $console->error('testing is not installed');
            $console->error('Please install testing using test:install command');
            exit(static::FAILURE_EXIT);
        }
    }

    public static function prefixRequired($console) {
        static::domainRequired($console);

        if (strlen(static::prefix()) == 0) {
            $console->error('Prefix is not set');
            $console->error('Please set your prefix in config app.php');
            exit(static::FAILURE_EXIT);
        }
    }

    /**
     * @return CConsole_Kernel
     */
    public static function kernel() {
        if (static::$kernel == null) {
            static::$kernel = new CConsole_Kernel();
            $commands = array_merge(CFConsole::$defaultCommands, CFConsole::$commands);
            static::$kernel->cfCli()->resolveCommands($commands);
        }

        return static::$kernel;
    }

    public static function stdout($output, $force = false) {
        if ($force || CF::isCli()) {
            fwrite(STDOUT, $output);
        }
    }
}
