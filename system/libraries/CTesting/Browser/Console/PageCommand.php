<?php

/**
 * Creates a new browser Page object in default/tests/Web/Pages/, extending
 * CTesting_Browser_Page.
 */
class CTesting_Browser_Console_PageCommand extends CConsole_GeneratorCommand {
    /**
     * @var string
     */
    protected $name = 'make:browser-page';

    /**
     * @var string
     */
    protected $description = 'Create a new browser page class';

    /**
     * @var string
     */
    protected $type = 'Page';

    /**
     * @return string
     */
    protected function getStub() {
        return CF::findFile('stubs', 'tests/browser/console/page', true, 'stub');
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
        return $rootNamespace . '\Web\Pages';
    }

    /**
     * @return string
     */
    protected function rootNamespace() {
        return 'Tests';
    }
}
