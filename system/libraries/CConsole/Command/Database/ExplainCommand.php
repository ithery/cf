<?php

class CConsole_Command_Database_ExplainCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:explain
               {--connection : Connection Name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Explain query and analyze';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $db = $this->getConnection();
        //$query = $this->argument('query');
        $query = $this->ask('Please type query');
        $analysis = CDatabase::analysis();
        $explainer = $analysis->setConnection($db)->getExplainer($query);

        $rows = $explainer->getRows();
        $headers = $explainer->getHeaderRow();
        $this->line('Found ' . count($rows) . ' row explain query' . (count($rows) > 1 ? 's' : ''));

        foreach ($rows as $rowIndex => $row) {
            $this->line('======================');
            $this->line('Row ' . ($rowIndex + 1));
            $this->line('======================');
            foreach ($row->getCells() as $cellIndex => $cell) {
                $this->line($cellIndex . ' (' . carr::get($headers, $cellIndex) . ')');

                $method = $cell->isDanger() ? 'error' : ($cell->isWarning() ? 'warn' : 'info');

                $this->$method($cell->v ?: '[NO VALUE]');
                $this->$method($cell->info);
                $this->$method('Link: ' . $explainer->getMysqlBaseDocUrl() . '#explain_' . strtolower($cellIndex));
                $this->line('---------------------');
            }
        }

        $hints = $explainer->getHints();
        if (count($hints) > 0) {
            $this->line('======================');
            $this->line('Hints');
            $this->line('======================');

            foreach ($hints as $hint) {
                $this->line($hint);
            }
        }

        return 0;
    }

    /**
     * Get the database connection configuration.
     *
     * @throws \UnexpectedValueException
     *
     * @return CDatabase_Connection
     */
    public function getConnection() {
        $connectionName = $this->option('connection') ?: 'default';
        $connection = c::db($connectionName);

        return $connection;
    }
}
