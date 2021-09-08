<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CConsole_Command_StatusCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status';

    public function handle() {
        $domain = CConsole::domain();
        if ($domain == null) {
            $this->error('Domain not set, please set with php cf domain:switch {domain}');
        }

        $this->info('Domain: ' . $domain);
        $this->output->newLine();

        $this->info('AppID: ' . CF::appId());
        $this->info('AppCode: ' . CF::appCode());
        $this->info('OrgID: ' . CF::orgId());
        $this->info('OrgCode: ' . CF::orgCode());
        $this->output->newLine();

        $db = CDatabase::instance();
        $config = $db->config();

        $configConnection = carr::get($config, 'connection');

        $rows = [];
        $rows[] = ['Type', carr::get($configConnection, 'type')];
        $rows[] = ['Host', carr::get($configConnection, 'host')];
        $rows[] = ['Port', carr::get($configConnection, 'port')];
        $rows[] = ['Username', carr::get($configConnection, 'user')];
        $rows[] = ['Database', carr::get($configConnection, 'database')];

        $this->info('Database Configuration');

        $this->info('======================');

        $this->table(['Description', 'Value'], $rows);
    }
}
