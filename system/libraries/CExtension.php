<?php

class CExtension {
    /**
     * @var CExtension_ModuleAbstract[]
     */
    protected $modules = [];

    private static $instance;

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->modules = [];
    }

    public function load($module) {
        if (is_string($module) && class_exists($module)) {
            $module = new $module();
        }
        $this->modules[] = $module;
    }

    protected function validateModule($module) {
        if (!$module instanceof CExtension_ModuleAbstract) {
            throw new Exception('Module must be instance of ' . CExtension_ModuleAbstract::class);
        }

        if ($module->getName() === null) {
            throw new Exception('Module must have name');
        }
    }

    /**
     * @return CExtension_ModuleAbstract[]
     */
    public function getLoadedExtensions() {
        return $this->modules;
    }
}
