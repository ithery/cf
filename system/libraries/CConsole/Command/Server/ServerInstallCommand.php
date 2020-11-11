<?php

/**
 * Description of ServerInstallCommand
 *
 * @author Hery
 */
use Symfony\Component\Console\Input\InputArgument;

class CConsole_Command_Server_ServerInstallCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install local development environment';
    protected $retryCount = 0;
    protected $appId = null;
    protected $appCode = null;
    protected $orgId = null;
    protected $orgCode = null;

    public function handle() {

        CServer_Service_Nginx::stop();

        Configuration::install();
        Nginx::install();
        PhpFpm::install();
        DnsMasq::install(Configuration::read()['tld']);
        Nginx::restart();
        Valet::symlinkToUsersBin();

        output(PHP_EOL . '<info>Valet installed successfully!</info>');
    }

}
