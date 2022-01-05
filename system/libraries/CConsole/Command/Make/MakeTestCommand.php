<?php

use Symfony\Component\Console\Input\InputOption;

class CConsole_Command_Make_MakeTestCommand extends CConsole_GeneratorCommand {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        $file = $this->option('unit') ? 'tests/unit/test' : 'tests/test';

        return $this->option('pest')
            ? $this->resolveStubPath($file)
            : $this->resolveStubPath($file);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolveStubPath($path) {
        $stubFile = CF::findFile('stubs', $path, true, 'stub');

        return $stubFile;
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name) {
        $name = cstr::replaceFirst($this->rootNamespace(), '', $name);

        return CF::appDir() . DS . 'default' . DS . 'tests' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        if ($this->option('unit')) {
            return $rootNamespace . '\Unit';
        } else {
            return $rootNamespace . '\Feature';
        }
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace() {
        return 'Tests';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['unit', 'u', InputOption::VALUE_NONE, 'Create a unit test.'],
            ['pest', 'p', InputOption::VALUE_NONE, 'Create a Pest test.'],
        ];
    }
}
