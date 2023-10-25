<?php

class CConsole_Command_Model_ModelShowCommand extends CConsole_Command_AppCommand {
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
     * @param string                         $class
     * @param string                         $database
     * @param string                         $table
     * @param string                         $policy
     * @param \Illuminate\Support\Collection $attributes
     * @param \Illuminate\Support\Collection $relations
     * @param \Illuminate\Support\Collection $observers
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
     * @param string                         $class
     * @param string                         $database
     * @param string                         $table
     * @param string                         $policy
     * @param \Illuminate\Support\Collection $attributes
     * @param \Illuminate\Support\Collection $relations
     * @param \Illuminate\Support\Collection $observers
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
                collect(['increments', 'unique', 'nullable', 'fillable', 'hidden', 'appended'])
                    ->filter(fn ($property) => $attribute[$property])
                    ->map(fn ($property) => sprintf('<fg=gray>%s</>', $property))
                    ->implode('<fg=gray>,</> ')
            ));

            $second = collect([
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

        $modelPrefix = $this->prefix . 'Model';
        if (!cstr::startsWith($model, $modelPrefix)) {
            $model = $modelPrefix . '_' . $model;
        }

        return $model;
    }
}
