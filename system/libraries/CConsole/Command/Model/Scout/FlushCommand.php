<?php

class CConsole_Command_Model_Scout_FlushCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:scout:flush {model : Class name of the model to flush}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Flush all of the model's records from the index";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $class = $this->argument('model');

        $model = new $class();

        $model::removeAllFromSearch();

        $this->info('All [' . $class . '] records have been flushed.');
    }
}
