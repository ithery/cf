<?php

class CConsole_Command_Model_ModelTablesCommand extends CConsole_Command_AppCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:tables';

    public function handle() {
        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $db = c::db();

        $tables = $db->query('show tables');

        $rows = c::collect($tables)->map(function ($element) use ($modelPath) {
            $table = '';
            foreach ($element as $key => $value) {
                $table = $value;
            }
            $model = $this->getModel($table);
            $modelFile = $modelPath . $model . EXT;
            $updated = '';

            if (!file_exists($modelFile)) {
                $model = '';
            } else {
                $updated = date('Y-m-d H:i:s', filemtime($modelFile));
            }

            return [
                'table' => $table,
                'model' => $model,
                'updated' => $updated,
            ];
        })->sortBy('table')->all();

        $this->table(['table', 'model', 'last update'], $rows);
    }

    private function getModel($table) {
        $model = $table;
        switch ($table) {
            case 'users':
                $model = 'user';

                break;
            case 'roles':
                $model = 'role';

                break;
        }

        $temp = explode('_', $model);
        $model = '';
        foreach ($temp as $val) {
            $model .= ucfirst($val);
        }

        return $model;
    }
}
