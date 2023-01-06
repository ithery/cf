<?php

class CConsole_Command_Model_Scout_DeleteAllIndexCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:scout:delete-all-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all indexes';

    /**
     * Execute the console command.
     *
     * @param \CModel_Scout_EngineManager $manager
     *
     * @return void
     */
    public function handle(CModel_Scout_EngineManager $manager) {
        $engine = $manager->engine();

        $driver = CF::config('model.scout.driver');

        if (!method_exists($engine, 'deleteAllIndexes')) {
            return $this->error('The [' . $driver . '] engine does not support deleting all indexes.');
        }

        try {
            $manager->engine()->deleteAllIndexes();

            $this->info('All indexes deleted successfully.');
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
