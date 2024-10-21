<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 2:34:11 AM
 */
class CManager_Asset_Module {
    const MODULE_TYPE_RUNTIME = 'runtime';

    const MODULE_TYPE_THEME = 'theme';

    protected static $instance;

    protected $unregisteredThemeModules = [];

    protected $modules = [];

    private $allModules = null;

    public function __construct() {
        $this->allModules = null;
        $this->modules = [];
        $this->modules[self::MODULE_TYPE_RUNTIME] = [];
        $this->modules[self::MODULE_TYPE_THEME] = [];

        $this->reset();
        $this->loadUnregisteredThemeModules();
    }

    public function reset() {
        $this->modules = [];
        $this->modules[self::MODULE_TYPE_RUNTIME] = [];
        $this->modules[self::MODULE_TYPE_THEME] = [];

        $this->unregisteredThemeModules = [];
    }

    public function allModules() {
        if ($this->allModules == null) {
            $this->allModules = [];
            $clientModulesFiles = CF::getFiles('config', 'client_modules');
            $assetsFiles = CF::getFiles('config', 'assets');

            //$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
            $clientModulesFiles = array_reverse($clientModulesFiles);
            $assetsFiles = array_reverse($assetsFiles);

            $systemModulesFile = DOCROOT . 'system' . DS . 'data' . DS . 'assets-module.php';
            $systemModules = include $systemModulesFile;
            $this->allModules = array_merge($this->allModules, $systemModules);

            foreach ($clientModulesFiles as $file) {
                $appModules = include $file;
                if (!is_array($appModules)) {
                    throw new CManager_Exception(c::__('Invalid client modules config format on :file', ['file' => $file]));
                }

                $this->allModules = array_merge($this->allModules, $appModules);
            }
            $assetModules = CF::config('assets.modules');
            if ($assetModules) {
                $this->allModules = array_merge($this->allModules, $assetModules);
            }
        }

        return $this->allModules;
    }

    public function requirements($module) {
        $data = [];
        $allModules = $this->allModules();
        if (isset($allModules[$module])) {
            $mod = $allModules[$module];
            if (isset($mod['requirements'])) {
                foreach ($mod['requirements'] as $req) {
                    $data_req = $this->requirements($req);
                    $data[] = $req;
                    $data = array_merge($data_req, $data);
                }
            }
        }

        return $data;
    }

    /**
     * @param string $mod
     *
     * @return bool
     */
    public function isRegisteredModule($mod) {
        $modules = array_merge($this->getRuntimeModules(), $this->getThemeModules());
        $inArray = in_array($mod, $modules);

        if (!$inArray) {
            $inArray = in_array($mod, $this->unregisteredThemeModules);
        }

        return $inArray;
    }

    public function getModules($type) {
        return $this->modules[$type];
    }

    public function getRuntimeModules() {
        return $this->getModules(self::MODULE_TYPE_RUNTIME);
    }

    public function getThemeModules() {
        return $this->getModules(self::MODULE_TYPE_THEME);
    }

    /**
     * @return array
     */
    public function getRegisteredModule() {
        return array_merge($this->getThemeModules(), $this->getRuntimeModules());
    }

    public function walkerCallback($tree, $node, $text) {
        if (is_array($text)) {
            $text = implode(',', $text);
        }

        return $text;
    }

    public function registerModules($type, $modules) {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        foreach ($modules as $module) {
            $this->registerModule($type, $module);
        }
    }

    public function defineModule($name, $moduleData) {
        //make sure all modules is collected
        $this->allModules();
        //replace or make new module
        $this->allModules[$name] = $moduleData;

        return $this->allModules;
    }

    public function unregisterModule($type, $module) {
        if (isset($this->modules[$type][$module])) {
            unset($this->modules[$type][$module]);

            return true;
        }

        return false;
    }

    public function registerModule($type, $module) {
        if ($type == self::MODULE_TYPE_RUNTIME) {
            if (in_array($module, $this->unregisteredThemeModules)) {
                //dont register when already defined in theme
                return true;
            }
        }
        $allModules = $this->allModules();
        if (!in_array($module, $this->modules[$type])) {
            if (!isset($allModules[$module])) {
                throw new CManager_Exception(c::__('Module :module not defined', [':module' => $module]));
            }
            //array
            $mod = $allModules[$module];
            if (isset($mod['requirements'])) {
                foreach ($mod['requirements'] as $req) {
                    $this->registerModule($type, $req);
                }
            }
            if (!in_array($module, $this->getModules($type))) {
                $this->modules[$type][$module] = $module;
            }
        }

        return true;
    }

    public function getContainer($type) {
        $container = $type == self::MODULE_TYPE_RUNTIME ? new CManager_Asset_Container_RunTime() : new CManager_Asset_Container_Theme();
        $allModules = $this->allModules();

        foreach ($this->getModules($type) as $module) {
            $mod = carr::get($allModules, $module, []);

            if (isset($mod['js'])) {
                $container->registerJsFiles($mod['js']);
            }
            if (isset($mod['css'])) {
                $container->registerCssFiles($mod['css']);
            }
        }

        return $container;
    }

    public function getRunTimeContainer() {
        return $this->getContainer(self::MODULE_TYPE_RUNTIME);
    }

    public function getThemeContainer() {
        return $this->getContainer(self::MODULE_TYPE_THEME);
    }

    /**
     * @return CManager_Asset_Module
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CManager_Asset_Module();
        }

        return self::$instance;
    }

    public function loadUnregisteredThemeModules() {
        $theme = CManager::theme()->getCurrentTheme();
        $themeFile = CF::getFile('themes', $theme);
        if (file_exists($themeFile)) {
            $themeData = include $themeFile;
            $this->unregisteredThemeModules = carr::get($themeData, 'client_modules');
        }
    }

    public function registerRunTimeModules($modules) {
        $this->registerModules(self::MODULE_TYPE_RUNTIME, $modules);
    }

    public function registerThemeModules($modules) {
        $this->registerModules(self::MODULE_TYPE_THEME, $modules);
    }

    public function unregisterRunTimeModule($module) {
        return $this->unregisterModule(self::MODULE_TYPE_RUNTIME, $module);
    }

    public function unregisterThemeModule($module) {
        return $this->unregisterModule(self::MODULE_TYPE_THEME, $module);
    }

    public function registerRunTimeModule($module) {
        return $this->registerModule(self::MODULE_TYPE_RUNTIME, $module);
    }

    public function registerThemeModule($module) {
        return $this->registerModule(self::MODULE_TYPE_THEME, $module);
    }
}
