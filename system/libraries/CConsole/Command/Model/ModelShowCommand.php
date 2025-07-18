<?php
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\DecimalType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

class CConsole_Command_Model_ModelShowCommand extends CConsole_Command_AppCommand {
    use CConsole_Command_Model_Trait_DatabaseInspectionTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'model:show {model}';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:show {model : The model to show}
                {--database= : The database connection to use}
                {--json : Output the model as JSON}';

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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show information about a model';

    public function handle() {
        $class = $this->qualifyModel($this->argument('model'));

        try {
            $model = c::container()->make($class);

            $class = get_class($model);
        } catch (CContainer_Exception_BindingResolutionException $e) {
            throw $e;
        }
        if ($this->option('database')) {
            $model->setConnection($this->option('database'));
        }
        $this->display(
            $class,
            $model->getConnection()->getName(),
            $model->getConnection()->getTablePrefix() . $model->getTable(),
            $this->getPolicy($model),
            $this->getAttributes($model),
            $this->getRelations($model),
            $this->getObservers($model),
        );
    }

    /**
     * Get the first policy associated with this model.
     *
     * @param \CModel $model
     *
     * @return string
     */
    protected function getPolicy($model) {
        $policy = CAuth_Access_Gate::instance()->getPolicyFor(get_class($model));

        return $policy ? get_class($policy) : null;
    }

    /**
     * Get the column attributes for the given model.
     *
     * @param \CModel $model
     *
     * @return \CCollection
     */
    protected function getAttributes($model) {
        $connection = $model->getConnection();
        $schema = $connection->getDoctrineSchemaManager();
        $this->registerTypeMappings($connection->getDoctrineConnection()->getDatabasePlatform());
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $columns = $schema->listTableColumns($table);
        $indexes = $schema->listTableIndexes($table);

        return c::collect($columns)
            ->values()
            ->map(function (Column $column) use ($model, $indexes) {
                return [
                    'name' => $column->getName(),
                    'type' => $this->getColumnType($column),
                    'increments' => $column->getAutoincrement(),
                    'nullable' => !$column->getNotnull(),
                    'default' => $this->getColumnDefault($column, $model),
                    'unique' => $this->columnIsUnique($column->getName(), $indexes),
                    'fillable' => $model->isFillable($column->getName()),
                    'hidden' => $this->attributeIsHidden($column->getName(), $model),
                    'appended' => null,
                    'cast' => $this->getCastType($column->getName(), $model),
                ];
            })
            ->merge($this->getVirtualAttributes($model, $columns));
    }

    /**
     * Get the virtual (non-column) attributes for the given model.
     *
     * @param \CModel                        $model
     * @param \Doctrine\DBAL\Schema\Column[] $columns
     *
     * @return CCollection
     */
    protected function getVirtualAttributes($model, $columns) {
        $class = new ReflectionClass($model);

        return c::collect($class->getMethods())
            ->reject(
                function (ReflectionMethod $method) {
                    return $method->isStatic()
                        || $method->isAbstract()
                        || $method->getDeclaringClass()->getName() === CModel::class;
                }
            )
            ->mapWithKeys(function (ReflectionMethod $method) use ($model) {
                if (preg_match('/^get(.+)Attribute$/', $method->getName(), $matches) === 1) {
                    return [cstr::snake($matches[1]) => 'accessor'];
                } elseif ($model->hasAttributeMutator($method->getName())) {
                    return [cstr::snake($method->getName()) => 'attribute'];
                } else {
                    return [];
                }
            })
            ->reject(fn ($cast, $name) => c::collect($columns)->has($name))
            ->map(fn ($cast, $name) => [
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
            ])
            ->values();
    }

    /**
     * Get the relations from the given model.
     *
     * @param \CModel $model
     *
     * @return CCollection
     */
    protected function getRelations($model) {
        return c::collect(get_class_methods($model))
            ->map(function ($method) use ($model) {
                return new ReflectionMethod($model, $method);
            })
            ->reject(
                function (ReflectionMethod $method) {
                    return $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() === CModel::class;
                }
            )
            ->filter(function (ReflectionMethod $method) {
                $file = new SplFileObject($method->getFileName());
                $file->seek($method->getStartLine() - 1);
                $code = '';
                while ($file->key() < $method->getEndLine()) {
                    $code .= trim($file->current());
                    $file->next();
                }

                return c::collect($this->relationMethods)
                    ->contains(function ($relationMethod) use ($code) {
                        return cstr::contains($code, '$this->' . $relationMethod . '(');
                    });
            })
            ->map(function (ReflectionMethod $method) use ($model) {
                $relation = $method->invoke($model);

                if (!$relation instanceof CModel_Relation) {
                    return null;
                }

                return [
                    'name' => $method->getName(),
                    'type' => cstr::afterLast(get_class($relation), '\\'),
                    'related' => get_class($relation->getRelated()),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Get the Observers watching this model.
     *
     * @param \CModel $model
     *
     * @return \CCollection
     */
    protected function getObservers($model) {
        $listeners = CEvent::dispatcher()->getRawListeners();

        // Get the Eloquent observers for this model...
        $listeners = array_filter($listeners, function ($v, $key) use ($model) {
            return cstr::startsWith($key, 'eloquent.') && cstr::endsWith($key, get_class($model));
        }, ARRAY_FILTER_USE_BOTH);

        // Format listeners Eloquent verb => Observer methods...
        $extractVerb = function ($key) {
            preg_match('/model.([a-zA-Z]+)\: /', $key, $matches);

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

        return c::collect($formatted);
    }

    /**
     * Render the model information.
     *
     * @param string       $class
     * @param string       $database
     * @param string       $table
     * @param string       $policy
     * @param \CCollection $attributes
     * @param \CCollection $relations
     * @param \CCollection $observers
     *
     * @return void
     */
    protected function display($class, $database, $table, $policy, $attributes, $relations, $observers) {
        $this->option('json')
            ? $this->displayJson($class, $database, $table, $policy, $attributes, $relations, $observers)
            : $this->displayCli($class, $database, $table, $policy, $attributes, $relations, $observers);
    }

    /**
     * Render the model information as JSON.
     *
     * @param string      $class
     * @param string      $database
     * @param string      $table
     * @param string      $policy
     * @param CCollection $attributes
     * @param CCollection $relations
     * @param CCollection $observers
     *
     * @return void
     */
    protected function displayJson($class, $database, $table, $policy, $attributes, $relations, $observers) {
        $this->output->writeln(
            c::collect([
                'class' => $class,
                'database' => $database,
                'table' => $table,
                'policy' => $policy,
                'attributes' => $attributes,
                'relations' => $relations,
                'observers' => $observers,
            ])->toJson()
        );
    }

    /**
     * Render the model information for the CLI.
     *
     * @param string      $class
     * @param string      $database
     * @param string      $table
     * @param string      $policy
     * @param CCollection $attributes
     * @param CCollection $relations
     * @param CCollection $observers
     *
     * @return void
     */
    protected function displayCli($class, $database, $table, $policy, $attributes, $relations, $observers) {
        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>' . $class . '</>');
        $this->components->twoColumnDetail('Database', $database);
        $this->components->twoColumnDetail('Table', $table);

        if ($policy) {
            $this->components->twoColumnDetail('Policy', $policy);
        }

        $this->newLine();

        $this->components->twoColumnDetail(
            '<fg=green;options=bold>Attributes</>',
            'type <fg=gray>/</> <fg=yellow;options=bold>cast</>',
        );

        foreach ($attributes as $attribute) {
            $first = trim(sprintf(
                '%s %s',
                $attribute['name'],
                c::collect(['increments', 'unique', 'nullable', 'fillable', 'hidden', 'appended'])
                    ->filter(fn ($property) => $attribute[$property])
                    ->map(fn ($property) => sprintf('<fg=gray>%s</>', $property))
                    ->implode('<fg=gray>,</> ')
            ));

            $second = c::collect([
                $attribute['type'],
                $attribute['cast'] ? '<fg=yellow;options=bold>' . $attribute['cast'] . '</>' : null,
            ])->filter()->implode(' <fg=gray>/</> ');

            $this->components->twoColumnDetail($first, $second);

            if ($attribute['default'] !== null) {
                $this->components->bulletList(
                    [sprintf('default: %s', $attribute['default'])],
                    OutputInterface::VERBOSITY_VERBOSE
                );
            }
        }

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Relations</>');

        foreach ($relations as $relation) {
            $this->components->twoColumnDetail(
                sprintf('%s <fg=gray>%s</>', $relation['name'], $relation['type']),
                $relation['related']
            );
        }

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Observers</>');

        if ($observers->count()) {
            foreach ($observers as $observer) {
                $this->components->twoColumnDetail(
                    sprintf('%s', $observer['event']),
                    implode(', ', $observer['observer'])
                );
            }
        }

        $this->newLine();
    }

    /**
     * Get the cast type for the given column.
     *
     * @param string  $column
     * @param \CModel $model
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

        return $this->getCastsWithDates($model)->get($column) ?? null;
    }

    /**
     * Get the model casts, including any date casts.
     *
     * @param \CModel $model
     *
     * @return \CCollection
     */
    protected function getCastsWithDates($model) {
        return c::collect($model->getDates())
            ->filter()
            ->flip()
            ->map(fn () => 'datetime')
            ->merge($model->getCasts());
    }

    /**
     * Get the type of the given column.
     *
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    protected function getColumnType($column) {
        $name = $column->getType()->getName();

        $unsigned = $column->getUnsigned() ? ' unsigned' : '';

        $classType = get_class($column->getType());
        if ($classType == DecimalType::class) {
            $details = $column->getPrecision() . ',' . $column->getScale();
        } else {
            $details = $column->getLength();
        }

        if ($details) {
            return sprintf('%s(%s)%s', $name, $details, $unsigned);
        }

        return sprintf('%s%s', $name, $unsigned);
    }

    /**
     * Get the default value for the given column.
     *
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param \CModel                      $model
     *
     * @return null|mixed
     */
    protected function getColumnDefault($column, $model) {
        $attributeDefault = $model->getAttributes()[$column->getName()] ?? null;

        // if ($attributeDefault instanceof BackedEnum) {
        //     return $attributeDefault->value;
        // }
        // if ($attributeDefault instanceof UnitEnum) {
        //     return $attributeDefault->name;
        // }

        return $attributeDefault ?? $column->getDefault();
    }

    /**
     * Determine if the given attribute is hidden.
     *
     * @param string  $attribute
     * @param \CModel $model
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
     * Determine if the given attribute is unique.
     *
     * @param string                        $column
     * @param \Doctrine\DBAL\Schema\Index[] $indexes
     *
     * @return bool
     */
    protected function columnIsUnique($column, $indexes) {
        return c::collect($indexes)
            ->filter(function (Index $index) use ($column) {
                return count($index->getColumns()) === 1 && $index->getColumns()[0] === $column;
            })
            ->contains(function (Index $index) {
                return $index->isUnique();
            });
    }

    /**
     * Qualify the given model class base name.
     *
     * @param string $model
     *
     * @return string
     *
     * @see \Illuminate\Console\GeneratorCommand
     */
    protected function qualifyModel(string $model) {
        if (cstr::contains($model, '\\') && class_exists($model)) {
            return $model;
        }
        $temp = explode('_', $model);
        $model = '';
        foreach ($temp as $val) {
            $model .= ucfirst($val);
        }
        $modelPrefix = $this->prefix . 'Model';
        if (!cstr::startsWith($model, $modelPrefix)) {
            $model = $modelPrefix . '_' . $model;
        }

        return $model;
    }
}
