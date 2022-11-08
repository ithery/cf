<?php

class CConsole_Command_Model_ModelUpdateCommand extends CConsole_Command_AppCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:update {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update model properties';

    public function handle() {
        $model = $this->getModel();
        $this->info('Updating ' . $model . ' model...');

        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $modelClass = $this->prefix . 'Model';
        if (!CFile::isDirectory($modelPath)) {
            CFile::makeDirectory($modelPath);
        }

        $modelFile = $modelPath . $model . EXT;

        if (!file_exists($modelFile)) {
            $this->warn('Model ' . $model . ' is not exist, please create it first using "make:model" command');

            return CConsole::FAILURE_EXIT;
        }

        $modelClass .= '_' . $model;
        $stubFile = CF::findFile('stubs', 'model', true, 'stub');
        if (!$stubFile) {
            $this->error('model stub not found');
            exit(1);
        }
        $content = CFile::get($modelFile);
        $content = preg_replace('/.*@property.*/', '{properties}', $content);
        $content = preg_replace('/{properties}/', $this->getUpdatedProperties(), $content, 1);
        $content = str_replace("{properties}\n", '', $content);

        CFile::put($modelFile, $content);

        $this->info($model . 'Model updated');
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

    private function getCurrentProperties() {
        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $modelFile = $modelPath . $this->getModel() . EXT;
        $content = CFile::get($modelFile);
        preg_match_all('/@property.*/', $content, $matches);
        $props = c::get($matches, 0);
        $result = [];
        foreach ($props as $prop) {
            $prop = preg_replace('/\s+/', ' ', $prop);
            $temp = explode(' ', $prop);
            $var = c::get($temp, 2);
            $result[] = [
                'prop' => c::get($temp, 0),
                'type' => c::get($temp, 1),
                'var' => $var,
                'field' => str_replace('$', '', $var),
            ];
        }

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
            'double unsigned' => 'double',
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

    public function compareField() {
        $compared = [];
        $currentProperties = $this->getCurrentProperties();
        $fileds = $this->getFields();
        $currentPropertiyFields = array_column($currentProperties, 'field');
        $classMethods = get_class_methods($this->prefix . 'Model_' . $this->getModel());

        foreach ($fileds as $field => $type) {
            $i = array_search($field, $currentPropertiyFields);
            if ($i === false) {
                $compared[$field] = 'add';
            } else {
                $prop = c::get($currentProperties, $i);
                $propType = c::get($prop, 'type');

                if ($propType !== $type) {
                    $compared[$field] = 'update';
                }
            }
        }

        foreach ($currentPropertiyFields as $field) {
            $i = array_search($field, array_keys($fileds));
            if ($i === false && !in_array($field, $classMethods)) {
                $compared[$field] = 'delete';
            }
        }

        return $compared;
    }

    public function getUpdatedProperties() {
        $properties = [];
        $fields = $this->getFields();
        $currentProperties = $this->getCurrentProperties();
        $compare = $this->compareField();

        foreach ($compare as $field => $status) {
            $i = array_search($field, array_column($currentProperties, 'field'));
            switch ($status) {
                case 'add':
                    $currentProperties[] = [
                        'prop' => $this->getTable() . '_id' === $field ? '@property-read' : '@property',
                        'type' => 'string',
                        'var' => '$' . $field,
                        'field' => $field
                    ];

                    break;
                case 'delete':
                    unset($currentProperties[$i]);

                    break;
                case 'update':
                    $currentProperties[$i]['type'] = $fields[$field];

                    break;
            }
        }

        foreach ($currentProperties as $property) {
            $properties[] = ' * ' . c::get($property, 'prop') . ' ' . c::get($property, 'type') . ' ' . c::get($property, 'var');
        }

        $result = implode("\n", $properties);

        return $result;
    }
}
