<?php
use CModel_Console_PropertiesHelper as Helper;

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
        $model = Helper::getModel($this->getTable());
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

        $content = CFile::get($modelFile);
        $content = preg_replace('/.*@property.*/', '{properties}', $content);
        $content = preg_replace('/{properties}/', $this->getUpdatedProperties(), $content, 1);
        $content = str_replace("{properties}\n", '', $content);

        CFile::put($modelFile, $content);

        $this->info($model . 'Model updated on ' . $modelFile);
    }

    private function getTable() {
        $table = $this->argument('table');

        return Helper::getTable($table);
    }

    private function getCurrentProperties() {
        $modelPath = c::fixPath(CF::appDir()) . 'default' . DS . 'libraries' . DS . $this->prefix . 'Model' . DS;
        $modelFile = $modelPath . Helper::getModel($this->getTable()) . EXT;
        $content = CFile::get($modelFile);
        preg_match_all('/@property.*/', $content, $matches);
        $props = c::get($matches, 0);
        $result = [];
        foreach ($props as $prop) {
            $prop = preg_replace('/\s+/', ' ', $prop);
            $temp = explode(' ', $prop);
            $var = carr::get($temp, 2);
            $desc = carr::get($temp, 3);
            if ($desc) {
                $desc = implode(' ', array_slice($temp, 3));
            }
            $result[] = [
                'prop' => c::get($temp, 0),
                'type' => c::get($temp, 1),
                'var' => $var,
                'field' => str_replace('$', '', $var),
                'desc' => $desc
            ];
        }

        return $result;
    }

    public function updateFieldProperties($properties) {
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
        while (true) {
            $missingIndex = $this->getMissingPropertyIndex($properties, $fields);
            if ($missingIndex !== false) {
                unset($properties[$missingIndex]);
            } else {
                break;
            }
        }

        return $properties;
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

    public function updateFieldRelation($properties) {
        $compared = [];

        $methods = Helper::getRelationMethods(Helper::getModelClass($this->prefix, $this->getTable()));
        $fields = carr::pluck($methods, 'method');
        $currentPropertyFields = array_column($properties, 'field');

        foreach ($methods as $methodIndex => $method) {
            $field = carr::get($method, 'method');
            $i = array_search($field, $currentPropertyFields);
            if ($i === false) {
                $properties[] = [
                    'prop' => '@property-read',
                    'type' => carr::get($method, 'type'),
                    'var' => '$' . $field,
                    'field' => $field,
                    'isRelation' => true,
                    'desc' => '',
                ];
            } else {
                $prop = carr::get($properties, $i);
                $propType = c::get($prop, 'type');

                if ($propType !== carr::get($method, 'type')) {
                    $properties[$i]['type'] = carr::get($method, 'type');
                }
            }
        }

        return $properties;
    }

    public function getUpdatedProperties() {
        $properties = [];

        $currentProperties = $this->getCurrentProperties();
        $currentProperties = $this->updateFieldProperties($currentProperties);
        $currentProperties = $this->updateFieldRelation($currentProperties);

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
}
