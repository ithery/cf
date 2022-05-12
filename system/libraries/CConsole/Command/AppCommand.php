<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CConsole_Command_AppCommand extends CConsole_Command {
    protected $prefix;

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->prefix = CF::config('app.prefix');

        if (strlen($this->prefix) == 0) {
            $this->error('Application prefix is required, make sure You on app directory. You can define it on app config using key "prefix"');
            exit;
        }

        return CContainer::getInstance()->call([$this, 'handle']);
    }
}
