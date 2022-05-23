<?php

use Symfony\Component\Console\Input\InputOption;

trait CConsole_Concern_CreatesMatchingTestTrait {
    /**
     * Add the standard command options for generating matching tests.
     *
     * @return void
     */
    protected function addTestOptions() {
        foreach (['test' => 'PHPUnit', 'pest' => 'Pest'] as $option => $name) {
            $this->getDefinition()->addOption(new InputOption(
                $option,
                null,
                InputOption::VALUE_NONE,
                "Generate an accompanying {$name} test for the {$this->type}"
            ));
        }
    }

    /**
     * Create the matching test case if requested.
     *
     * @param string $path
     *
     * @return void
     */
    protected function handleTestCreation($path) {
        if (!$this->option('test') && !$this->option('pest')) {
            return;
        }

        $this->call('make:test', [
            'name' => cstr::of($path)->after(CF::appDir())->beforeLast('.php')->append('Test')->replace('\\', '/'),
            '--pest' => $this->option('pest'),
        ]);
    }
}
