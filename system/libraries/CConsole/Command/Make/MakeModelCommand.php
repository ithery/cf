<?php

class CConsole_Command_Make_MakeModelCommand extends CConsole_Command_AppCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class based on created table on database';

    public function handle() {
        $model = $this->getModel();
        $table = $this->getTable();
        $this->info('Creating ' . $model . ' model...');

        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $modelClass = $this->prefix . 'Model';
        if (!CFile::isDirectory($modelPath)) {
            CFile::makeDirectory($modelPath);
        }

        $modelFile = $modelPath . $model . EXT;

        if (file_exists($modelFile)) {
            $this->info('Model ' . $model . ' already created, no changes');

            return CConsole::SUCCESS_EXIT;
        }

        $modelClass .= '_' . $model;
        $stubFile = CF::findFile('stubs', 'model', true, 'stub');
        if (!$stubFile) {
            $this->error('model stub not found');
            exit(1);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{ModelClass}', $modelClass, $content);
        $content = str_replace('{prefix}', $this->prefix, $content);
        $content = str_replace('{table}', $table, $content);
        $content = str_replace('{primaryKey}', $table . '_id', $content);
        $content = str_replace('{properties}', $this->getProperties(), $content);

        CFile::put($modelFile, $content);

        $this->info($model . 'Model created on:' . $modelFile);
    }

    private function getTable() {
        $table = $this->argument('table');
        switch ($table) {
            case 'user':
                $table = 'users';

                break;
            case 'role':
                $table = 'roles';

                break;
        }

        return $table;
    }

    private function getModel() {
        $model = $table = $this->argument('table');
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

    private function getProperties() {
        $properties = [];
        $fields = $this->getFields();
        foreach ($fields as $field => $type) {
            if (strpos($type, ' unsigned') !== false) {
                $type = cstr::replace(' unsigned', '', $type);
            }
            if ($field == $this->getTable() . '_id') {
                $properties[] = " * @property-read ${type} $${field}";
            } else {
                $properties[] = " * @property ${type} $${field}";
            }
        }

        $result = implode("\n", $properties);

        return $result;
    }

    private function getFields() {
        $table = $this->getTable();
        $excludedFields = ['created', 'createdby', 'updated', 'updatedby', 'deleted', 'deletedby', 'status'];
        $db = c::db();

        $result = $db->query("desc ${table}");
        $properties = [];
        foreach ($result as $value) {
            $field = $value->Field;
            $type = $value->Type;
            $temp = explode('(', $type);
            if ($temp) {
                $type = c::get($temp, 0);
            }
            $type = $this->getType($type);
            if (!in_array($field, $excludedFields)) {
                $properties[$field] = $type;
            }
        }

        return $properties;
    }

    private function getType(string $type) {
        $typeConvertion = [
            'tinyint' => 'int',
            'smallint' => 'int',
            'mediumint' => 'int',
            'bigint' => 'int',
            'decimal' => 'int',
            'float' => 'float',
            'double' => 'double',
            'bit' => 'int',
            'char' => 'string',
            'varchar' => 'string',
            'binary' => 'string',
            'varbinary' => 'string',
            'tinyblob' => 'string',
            'blob' => 'string',
            'mediumblob' => 'string',
            'longblob' => 'string',
            'tinytext' => 'string',
            'text' => 'string',
            'mediumtext' => 'string',
            'longtext' => 'string',
            'enum' => 'string',
            'set' => 'string',
            'date' => 'CCarbon',
            'time' => 'string',
            'datetime' => 'CCarbon',
            'timestamp' => 'string',
            'year' => 'string',
            'boolean' => 'bool',
        ];

        if ($result = c::get($typeConvertion, $type)) {
            return $result;
        }

        return $type;
    }
}
