<?php

/**
 * Creates a new browser test class in default/tests/Web/, extending
 * AbstractBrowserTestCase (see test:browser-install). Mirrors
 * CConsole_Command_Make_MakeTestCommand's pattern for the Unit/Feature suites.
 */
class CTesting_Browser_Console_MakeCommand extends CConsole_GeneratorCommand {
    /**
     * @var string
     */
    protected $name = 'make:browser-test';

    /**
     * @var string
     */
    protected $description = 'Create a new browser test class';

    /**
     * @var string
     */
    protected $type = 'Test';

    /**
     * @return string
     */
    protected function getStub() {
        return CF::findFile('stubs', 'tests/browser/console/test', true, 'stub');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name) {
        $name = cstr::replaceFirst($this->rootNamespace(), '', $name);

        return CF::appDir() . DS . 'default' . DS . 'tests' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Web';
    }

    /**
     * @return string
     */
    protected function rootNamespace() {
        return 'Tests';
    }
}
