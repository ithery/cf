<?php

/**
 * Creates a new browser Component object in default/tests/Web/Components/,
 * extending CTesting_Browser_Component.
 */
class CTesting_Browser_Console_ComponentCommand extends CConsole_GeneratorCommand {
    /**
     * @var string
     */
    protected $name = 'make:browser-component';

    /**
     * @var string
     */
    protected $description = 'Create a new browser component class';

    /**
     * @var string
     */
    protected $type = 'Component';

    /**
     * @return string
     */
    protected function getStub() {
        return CF::findFile('stubs', 'tests/browser/console/component', true, 'stub');
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
        return $rootNamespace . '\Web\Components';
    }

    /**
     * @return string
     */
    protected function rootNamespace() {
        return 'Tests';
    }
}
