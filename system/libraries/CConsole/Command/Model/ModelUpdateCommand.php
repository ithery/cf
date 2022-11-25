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

    private function getRelationMethods() {
        //get relation field
        $reflectionClass = new ReflectionClass($this->getModelClass());
        $blacklistMethods = [
            'belongsTo'
        ];
        $returnTypeClasses = [
            CModel_Relation_BelongsTo::class,
            CModel_Relation_BelongsToThrough::class,
            CModel_Relation_BelongsToMany::class,
            CModel_Relation_HasMany::class,
            CModel_Relation_HasManyThrough::class,
            CModel_Relation_HasManyDeep::class,
            CModel_Relation_MorphMany::class,
            CModel_Relation_HasOne::class,
        ];

        $methods = c::collect($reflectionClass->getMethods())->map(function (ReflectionMethod $method) use ($returnTypeClasses, $blacklistMethods) {
            if (in_array($method->getName(), $blacklistMethods)) {
                //skip when method is blacklisted to be processed
                return false;
            }
            if ($method->getFileName() != $method->getDeclaringClass()->getFileName()) {
                //skip when method is not in same file
                return false;
            }
            $docComment = $method->getDocComment();

            if ($docComment) {
                if (strpos($docComment, '@return') !== false) {
                    if (preg_match('#\@return\s(.+?)\n#ims', $docComment, $matches)) {
                        $matches = array_slice($matches, 1);
                        foreach ($matches as $match) {
                            $returnTypes = explode('|', $match);
                            foreach ($returnTypes as $returnType) {
                                if (in_array($returnType, $returnTypeClasses)) {
                                    list($relationClass, $isWithTrashed) = $this->getRelationClass($method);
                                    if ($relationClass && $relationType = $this->getRelationType($returnType, $relationClass, $isWithTrashed)) {
                                        return [
                                            'method' => $method->getName(),
                                            'returnType' => $returnType,
                                            'relationClass' => $relationClass,
                                            'type' => $relationType,
                                            'isWithTrashed' => $isWithTrashed,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return false;
        })->filter()->toArray();

        return $methods;
    }

    private function getRelationType($returnType, $relationClass, $isWithTrashed) {
        $relationType = null;
        if ($returnType == CModel_Relation_BelongsTo::class
            || $returnType == CModel_Relation_BelongsToThrough::class
        ) {
            $relationType = $relationClass;
            if (!$isWithTrashed) {
                $relationType = 'null|' . $relationType;
            }
        }

        if ($returnType == CModel_Relation_BelongsToMany::class
            || $returnType == CModel_Relation_HasMany::class
            || $returnType == CModel_Relation_MorphMany::class
            || $returnType == CModel_Relation_HasManyThrough::class
            || $returnType == CModel_Relation_HasManyDeep::class
        ) {
            $relationType = 'CModel_Collection|' . $relationClass . '[]';
        }
        if ($returnType == CModel_Relation_HasOne::class) {
            $relationType = 'null|' . $relationClass;
        }

        return $relationType;
    }

    private function getRelationClass(ReflectionMethod $method) {
        $codeSnippet = $this->getCodeSnippet($method->getFileName(), $method->getStartLine(), $method->getEndLine());
        $regex = '#\$this->.+?\((.+?)[\,\)]#ims';
        if (preg_match($regex, $codeSnippet, $matches)) {
            $relationClass = trim($matches[1]);
            if (cstr::endsWith($relationClass, '::class')) {
                $relationClass = cstr::substr($relationClass, 0, cstr::len($relationClass) - 7);
            }
            if (cstr::len($relationClass) > 1 && cstr::startsWith($relationClass, '\'') && cstr::endsWith($relationClass, '\'')) {
                $relationClass = cstr::substr($relationClass, 1, cstr::len($relationClass) - 2);
            }

            if (cstr::startsWith($relationClass, [')', '->', '$'])) {
                return [null, null];
            }
            $isWithTrashed = strpos($codeSnippet, '->withTrashed') !== false;

            return [$relationClass, $isWithTrashed];
        }

        return [null, null];
    }

    private function getCodeSnippet($path, $startLine, $endLine) {
        if ($endLine < $startLine) {
            return [];
        }
        $file = new SplFileObject($path);
        $file->seek($startLine - 1);
        $code = [];
        $code[] = $file->current();
        for ($i = $startLine; $i < $endLine; $i++) {
            $file->next();
            $code[] = $file->current();
        }

        return implode(PHP_EOL, $code);
    }

    private function getFields() {
        $table = $this->getTable();
        $excludedFields = ['created', 'createdby', 'updated', 'updatedby', 'status'];
        $db = c::db();

        $result = $db->getSchemaManager()->listTableColumns($table);

        $properties = [];
        $modelInstance = $this->getModelInstance();

        foreach ($result as $key => $column) {
            /** @var CDatabase_Schema_Column $column */
            $field = trim($key, '`');
            $type = $column->getType()->getName();
            if ($type == 'boolean') {
                //when type is boolean, we cast it on int first then cast again when cast defined
                $type = 'int';
            }
            $casts = $modelInstance->getCasts();

            $type = carr::get($casts, $field, $type);
            $type = $this->getType($type);

            if (!in_array($field, $excludedFields)) {
                $properties[$field] = [
                    'type' => $type,
                    'notnull' => $column->getNotnull(),
                    'default' => $column->getDefault(),
                ];
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
            'date' => 'CCarbon|\Carbon\Carbon',
            'time' => 'string',
            'datetime' => 'CCarbon|\Carbon\Carbon',
            'timestamp' => 'string',
            'year' => 'string',
            'boolean' => 'bool',
            //casts convertion
            'integer' => 'int',
            'array' => 'array',
            'json' => 'array',
        ];

        if ($result = carr::get($typeConvertion, $type)) {
            return $result;
        }

        return $type;
    }

    public function getModelClass() {
        return  $this->prefix . 'Model_' . $this->getModel();
    }

    /**
     * @return CModel
     */
    public function getModelInstance() {
        $modelClass = $this->getModelClass();

        return new $modelClass();
    }

    private function getGenericTypeForFieldProperty(array $property) {
        $type = carr::get($property, 'type');
        if (!carr::get($property, 'notnull')) {
            $type = 'null|' . $type;
        }

        return $type;
    }

    public function updateFieldProperties($properties) {
        $fields = $this->getFields();
        $currentPropertyFields = array_column($properties, 'field');
        foreach ($fields as $field => $fieldProperty) {
            $type = $this->getGenericTypeForFieldProperty($fieldProperty);
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
        $fieldsKey = c::collect($this->getFields())->keys()->toArray();
        $classMethods = get_class_methods($this->prefix . 'Model_' . $this->getModel());
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

        $methods = $this->getRelationMethods();
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
