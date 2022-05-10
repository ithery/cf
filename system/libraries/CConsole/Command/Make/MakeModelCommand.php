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
        CConsole::domainRequired($this);
        $prefix = CF::config('app.prefix');
        if (strlen($prefix) == 0) {
            $this->error('Application prefix is required, you can define it on app config using key "prefix"');

            return CConsole::FAILURE_EXIT;
        }
        $model = $this->argument('model');
        $this->info('Creating ' . ucfirst($model) . ' model...');

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
        $stubFile = CF::findFile('stubs', 'model', true, 'stub');
        if (!$stubFile) {
            $this->error('model stub not found');
            exit(1);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{ModelClass}', $modelClass, $content);
        $content = str_replace('{prefix}', $prefix, $content);
        $content = str_replace('{table}', $model, $content);
        $content = str_replace('{primaryKey}', $model . '_id', $content);
        $content = str_replace('{properties}', $this->getProperties(), $content);

        CFile::put($modelFile, $content);

        $this->info(ucfirst($model) . 'Model created on:' . $modelFile);
    }

    private function getTable() {
        $table = $model = $this->argument('model');
        switch ($model) {
            case 'user':
                $table = 'users';

                break;
            case 'role':
                $table = 'roles';

                break;
        }

        return $table;
    }

    private function getProperties() {
        $properties = [];
        $fields = $this->getFields();
        foreach ($fields as $field => $type) {
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
            'datetime' => 'string',
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
