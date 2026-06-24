<?php

defined('SYSPATH') or die('No direct access allowed.');

class CModel_ModelInspector {
    /**
     * The methods that can be called in a model to indicate a relation.
     *
     * @var array
     */
    protected $relationMethods = [
        'hasMany',
        'hasManyThrough',
        'hasOneThrough',
        'belongsToMany',
        'hasOne',
        'belongsTo',
        'morphOne',
        'morphTo',
        'morphMany',
        'morphToMany',
        'morphedByMany',
    ];

    /**
     * Extract model details for the given model.
     *
     * @param string      $model
     * @param null|string $connection
     *
     * @return CModel_ModelInfo
     */
    public function inspect($model, $connection = null) {
        $class = $this->qualifyModel($model);

        /** @var CModel $model */
        $model = new $class();

        if ($connection !== null) {
            $model->setConnection($connection);
        }

        return new CModel_ModelInfo(
            get_class($model),
            $model->getConnection()->getName(),
            $model->getConnection()->getTablePrefix() . $model->getTable(),
            $this->getPolicy($model),
            $this->getAttributes($model),
            $this->getRelations($model),
            $this->getEvents($model),
            $this->getObservers($model),
            $this->getCollectedBy($model),
            $this->getBuilder($model),
            $this->getResource($model)
        );
    }

    /**
     * Get the column attributes for the given model.
     *
     * @param CModel $model
     *
     * @return CCollection
     */
    protected function getAttributes($model) {
        $connection = $model->getConnection();
        $schema = $connection->getSchemaBuilder();
        $table = $model->getTable();
        $columns = $schema->getColumns($table);
        $indexes = $schema->getIndexes($table);

        return (new CCollection($columns))
            ->map(function ($column) use ($model, $indexes) {
                return [
                    'name' => $column['name'],
                    'type' => $column['type'],
                    'increments' => $column['auto_increment'],
                    'nullable' => $column['nullable'],
                    'default' => $this->getColumnDefault($column, $model),
                    'unique' => $this->columnIsUnique($column['name'], $indexes),
                    'fillable' => $model->isFillable($column['name']),
                    'hidden' => $this->attributeIsHidden($column['name'], $model),
                    'appended' => null,
                    'cast' => $this->getCastType($column['name'], $model),
                ];
            })
            ->merge($this->getVirtualAttributes($model, $columns));
    }

    /**
     * Get the virtual (non-column) attributes for the given model.
     *
     * @param CModel $model
     * @param array  $columns
     *
     * @return CCollection
     */
    protected function getVirtualAttributes($model, $columns) {
        $class = new ReflectionClass($model);

        return (new CCollection($class->getMethods()))
            ->reject(function (ReflectionMethod $method) {
                return $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() === CModel::class;
            })
            ->mapWithKeys(function (ReflectionMethod $method) use ($model) {
                if (preg_match('/^get(.+)Attribute$/', $method->getName(), $matches) === 1) {
                    return [cstr::snake($matches[1]) => 'accessor'];
                } elseif ($model->hasAttributeMutator($method->getName())) {
                    return [cstr::snake($method->getName()) => 'attribute'];
                } else {
                    return [];
                }
            })
            ->reject(function ($cast, $name) use ($columns) {
                return (new CCollection($columns))->contains('name', $name);
            })
            ->map(function ($cast, $name) use ($model) {
                return [
                    'name' => $name,
                    'type' => null,
                    'increments' => false,
                    'nullable' => null,
                    'default' => null,
                    'unique' => null,
                    'fillable' => $model->isFillable($name),
                    'hidden' => $this->attributeIsHidden($name, $model),
                    'appended' => $model->hasAppended($name),
                    'cast' => $cast,
                ];
            })
            ->values();
    }

    /**
     * Get the relations from the given model.
     *
     * @param CModel $model
     *
     * @return CCollection
     */
    protected function getRelations($model) {
        return (new CCollection(get_class_methods($model)))
            ->map(function ($method) use ($model) {
                return new ReflectionMethod($model, $method);
            })
            ->reject(function (ReflectionMethod $method) {
                return $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() === CModel::class
                    || $method->getNumberOfParameters() > 0;
            })
            ->filter(function (ReflectionMethod $method) {
                if ($method->getReturnType() instanceof ReflectionNamedType
                    && is_subclass_of($method->getReturnType()->getName(), CModel_Relation::class)
                ) {
                    return true;
                }

                $file = new SplFileObject($method->getFileName());
                $file->seek($method->getStartLine() - 1);
                $code = '';
                while ($file->key() < $method->getEndLine()) {
                    $code .= trim($file->current());
                    $file->next();
                }

                $relationMethods = $this->relationMethods;

                return (new CCollection($relationMethods))
                    ->contains(function ($relationMethod) use ($code) {
                        return strpos($code, '$this->' . $relationMethod . '(') !== false;
                    });
            })
            ->map(function (ReflectionMethod $method) use ($model) {
                $relation = $method->invoke($model);

                if (!$relation instanceof CModel_Relation) {
                    return null;
                }

                return [
                    'name' => $method->getName(),
                    'type' => cstr::afterLast(get_class($relation), '_'),
                    'related' => get_class($relation->getRelated()),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Get the first policy associated with this model.
     *
     * @param CModel $model
     *
     * @return null|string
     */
    protected function getPolicy($model) {
        $policy = c::gate()->getPolicyFor(get_class($model));

        return $policy ? get_class($policy) : null;
    }

    /**
     * Get the events that the model dispatches.
     *
     * @param CModel $model
     *
     * @return CCollection
     */
    protected function getEvents($model) {
        $ref = new ReflectionClass($model);
        $events = [];
        if ($ref->hasProperty('dispatchesEvents')) {
            $prop = $ref->getProperty('dispatchesEvents');
            $prop->setAccessible(true);
            $events = $prop->getValue($model);
        }

        return (new CCollection($events))
            ->map(function ($class, $event) {
                return [
                    'event' => $event,
                    'class' => $class,
                ];
            })->values();
    }

    /**
     * Get the observers watching this model.
     *
     * @param CModel $model
     *
     * @return CCollection
     */
    protected function getObservers($model) {
        $listeners = CEvent::dispatcher()->getRawListeners();

        $modelClass = get_class($model);

        $listeners = array_filter($listeners, function ($v, $key) use ($modelClass) {
            return cstr::startsWith($key, 'eloquent.') && cstr::endsWith($key, $modelClass);
        }, ARRAY_FILTER_USE_BOTH);

        $extractVerb = function ($key) {
            preg_match('/eloquent.([a-zA-Z]+)\: /', $key, $matches);

            return $matches[1] ?? '?';
        };

        $formatted = [];

        foreach ($listeners as $key => $observerMethods) {
            $formatted[] = [
                'event' => $extractVerb($key),
                'observer' => array_map(function ($obs) {
                    return is_string($obs) ? $obs : 'Closure';
                }, $observerMethods),
            ];
        }

        return new CCollection($formatted);
    }

    /**
     * Get the collection class being used by the model.
     *
     * @param CModel $model
     *
     * @return string
     */
    protected function getCollectedBy($model) {
        return get_class($model->newCollection());
    }

    /**
     * Get the builder class being used by the model.
     *
     * @param CModel $model
     *
     * @return string
     */
    protected function getBuilder($model) {
        return get_class($model->newQuery());
    }

    /**
     * Get the class used for JSON response transforming.
     *
     * @param CModel $model
     *
     * @return null|string
     */
    protected function getResource($model) {
        return c::rescue(function () use ($model) {
            return method_exists($model, 'toResource') ? get_class($model->toResource()) : null;
        }, null, false);
    }

    /**
     * Qualify the given model class base name.
     *
     * @param string $model
     *
     * @return string
     */
    protected function qualifyModel($model) {
        if (class_exists($model)) {
            return $model;
        }

        return $model;
    }

    /**
     * Get the cast type for the given column.
     *
     * @param string $column
     * @param CModel $model
     *
     * @return null|string
     */
    protected function getCastType($column, $model) {
        if ($model->hasGetMutator($column) || $model->hasSetMutator($column)) {
            return 'accessor';
        }

        if ($model->hasAttributeMutator($column)) {
            return 'attribute';
        }

        return $this->getCastsWithDates($model)->get($column);
    }

    /**
     * Get the model casts, including any date casts.
     *
     * @param CModel $model
     *
     * @return CCollection
     */
    protected function getCastsWithDates($model) {
        return (new CCollection($model->getDates()))
            ->filter()
            ->flip()
            ->map(function () {
                return 'datetime';
            })
            ->merge($model->getCasts());
    }

    /**
     * Determine if the given attribute is hidden.
     *
     * @param string $attribute
     * @param CModel $model
     *
     * @return bool
     */
    protected function attributeIsHidden($attribute, $model) {
        if (count($model->getHidden()) > 0) {
            return in_array($attribute, $model->getHidden());
        }

        if (count($model->getVisible()) > 0) {
            return !in_array($attribute, $model->getVisible());
        }

        return false;
    }

    /**
     * Get the default value for the given column.
     *
     * @param array  $column
     * @param CModel $model
     *
     * @return mixed
     */
    protected function getColumnDefault($column, $model) {
        $attributeDefault = isset($model->getAttributes()[$column['name']])
            ? $model->getAttributes()[$column['name']]
            : null;

        return c::enumValue($attributeDefault) ?? $column['default'];
    }

    /**
     * Determine if the given attribute is unique.
     *
     * @param string $column
     * @param array  $indexes
     *
     * @return bool
     */
    protected function columnIsUnique($column, $indexes) {
        return (new CCollection($indexes))->contains(function ($index) use ($column) {
            return count($index['columns']) === 1 && $index['columns'][0] === $column && $index['unique'];
        });
    }
}
