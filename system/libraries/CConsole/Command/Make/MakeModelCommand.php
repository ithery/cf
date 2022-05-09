<?php

class CConsole_Command_Make_MakeModelCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class based on created table on database';

    public function handle() {
        $this->warn('this command is uncompleted');

        CConsole::domainRequired($this);
        $prefix = CF::config('app.prefix');
        if (strlen($prefix) == 0) {
            $this->error('Application prefix is required, you can define it on app config using key "prefix"');

            return CConsole::FAILURE_EXIT;
        }
        $model = $this->argument('model');
        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $prefix . 'Model' . DS;
        $modelClass = $prefix . 'Model';
        if (!CFile::isDirectory($modelPath)) {
            CFile::makeDirectory($modelPath);
        }

        $modelFile = $modelPath . ucfirst($model) . EXT;

        if (file_exists($modelFile)) {
            $this->info('Model ' . $model . ' already created, no changes');

            return CConsole::SUCCESS_EXIT;
        }
        $modelClass .= '_' . ucfirst($model);
        $stubFile = CF::findFile('stubs', 'controller', true, 'stub');
        if (!$stubFile) {
            $this->error('model stub not found');
            exit(1);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{ModelClass}', $modelClass, $content);
        $content = str_replace('{prefix}', $prefix, $content);

        // CFile::put($modelFile, $content);

        $this->info('Model ' . $model . ' created on:' . $modelFile);
    }
}
