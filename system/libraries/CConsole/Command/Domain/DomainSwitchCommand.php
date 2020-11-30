<?php

/**
 * Description of DomainSwitchCommand
 *
 * @author Hery
 */
class CConsole_Command_Domain_DomainSwitchCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:switch {domain}';

    public function handle() {
        $domain = $this->argument('domain');
        $this->info('Switching to ' . $domain);

        $fileData = DOCROOT . 'data/current-domain';
        cfs::atomic_write($fileData, $domain);
        $this->info('Switched to ' . $domain);
    }

}
