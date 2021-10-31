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

        $this->databaseInformation();
    }

    protected function databaseInformation() {
        $db = CDatabase::instance();

        $this->info('Database Information');

        $this->info('======================');

        $results = new CConsole_Result();
        $connectionName = $db->getName();
        $results->add(
            'Connection name',
            $connectionName
        );

        $tablePrefix = $db->getTablePrefix();
        $results->add(
            'Table prefix',
            $tablePrefix
        );

        $driverName = $db->driverName();
        $results->add(
            'Driver name',
            $driverName
        );

        $databaseName = $db->getDatabaseName();
        $results->add(
            'Database name',
            $databaseName
        );

        $results->printToConsole($this, ['Description', 'Value']);
    }
}
