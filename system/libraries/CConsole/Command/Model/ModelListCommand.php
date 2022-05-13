<?php

class CConsole_Command_Model_ModelListCommand extends CConsole_Command_AppCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:list';

    public function handle() {
        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $allFiles = cfs::list_files($modelPath);

        $rows = c::collect($allFiles)->map(function ($file) {
            $model = basename($file);
            if (substr($model, -4) == '.php') {
                $model = substr($model, 0, strlen($model) - 4);
            }

            return [
                'model' => $model,
                'updated' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        })->sortBy('model')->all();

        $this->table(['model', 'last update'], $rows);
    }
}
