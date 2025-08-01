<?php

use CModel_Console_PropertiesHelper as Helper;

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
        $table = $this->getTable();
        $model = Helper::getModel($table);
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

        return Helper::getTable($table);
    }

    public function getFieldProperties($properties = []) {
        $fields = Helper::getFields($this->getTable(), $this->prefix);
        $currentPropertyFields = array_column($properties, 'field');
        foreach ($fields as $field => $fieldProperty) {
            $type = Helper::getGenericTypeForFieldProperty($fieldProperty);
            $i = array_search($field, $currentPropertyFields);
            if ($i === false) {
                $properties[] = [
                    'prop' => $this->getTable() . '_id' === $field ? '@property-read' : '@property',
                    'type' => $type,
                    'var' => '$' . $field,
                    'field' => $field,
                    'desc' => '',
                ];
            } else {
                $prop = carr::get($properties, $i);
                $propType = c::get($prop, 'type');

                if ($propType !== $type) {
                    $properties[$i]['type'] = $type;
                }
            }
        }

        return $properties;
    }

    private function getProperties() {
        $properties = [];
        $currentProperties = $this->getFieldProperties();
        $propLength = 0;
        $typeLength = 0;
        $varLength = 0;

        $propLength = 0;
        $typeLength = 0;
        $varLength = 0;

        foreach ($currentProperties as $property) {
            if ($propLength < strlen(carr::get($property, 'prop'))) {
                $propLength = strlen(carr::get($property, 'prop'));
            }
            if ($typeLength < strlen(carr::get($property, 'type'))) {
                $typeLength = strlen(carr::get($property, 'type'));
            }
            if ($varLength < strlen(carr::get($property, 'var'))) {
                $varLength = strlen(carr::get($property, 'var'));
            }
        }
        foreach ($currentProperties as $property) {
            $prop = ' * ';
            $prop .= cstr::padRight(carr::get($property, 'prop'), $propLength);
            $prop .= ' ' . cstr::padRight(carr::get($property, 'type'), $typeLength);
            if (carr::get($property, 'desc')) {
                $prop .= ' ' . cstr::padRight(carr::get($property, 'var'), $varLength);
                $prop .= ' ' . carr::get($property, 'desc');
            } else {
                $prop .= ' ' . carr::get($property, 'var');
            }
            $properties[] = $prop;
        }

        $result = implode("\n", $properties);

        return $result;
    }

    private function getMissingPropertyIndex($properties) {
        $fieldsKey = c::collect(Helper::getFields($this->getTable(), $this->prefix))->keys()->toArray();
        $classMethods = get_class_methods($this->prefix . 'Model_' . Helper::getModel($this->getTable()));
        foreach ($properties as $index => $property) {
            $field = carr::get($property, 'field');
            $i = array_search($field, $fieldsKey);
            if ($i === false && !in_array($field, $classMethods)) {
                if (!cstr::endsWith($field, '_count')) {
                    return $index;
                }
            }
        }

        return false;
    }
}
