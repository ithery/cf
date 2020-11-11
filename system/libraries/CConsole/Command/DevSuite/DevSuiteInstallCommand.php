<?php

/**
 * Description of DevSuiteInstallCommand
 *
 * @author Hery
 */
use Symfony\Component\Console\Input\InputArgument;

class CConsole_Command_DevSuite_DevSuiteInstallCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devsuite:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all devsuite environment';

    public function handle() {

        CDevSuite_Nginx::stop();

        Configuration::install();
        Nginx::install();
        PhpFpm::install();
        DnsMasq::install(Configuration::read()['tld']);
        Nginx::restart();
        Valet::symlinkToUsersBin();

        output(PHP_EOL . '<info>Valet installed successfully!</info>');
    }

}
