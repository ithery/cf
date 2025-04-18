<?php

class CConsole_Command_Database_SchemaCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:schema {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the schema for a given table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        // Get the model, and try to set the namespace automatically
        $table = $this->argument('table');
        $tableDescription = c::db()->select('DESCRIBE ' . $table);

        // $this->info('Schema for Model: ' . $modelPath);
        $this->comment('Table: ' . $table);

        if (count($tableDescription) > 0) {
            $rows = [];

            foreach ($tableDescription as $field) {
                $rowData = [];

                foreach ($field as $value) {
                    $rowData[] = $value;
                }

                $rows[] = $rowData;
            }

            $this->table([
                'Field',
                'Type',
                'Null',
                'Key',
                'Default',
                'Extra'
            ], $rows);
        }
    }
}
