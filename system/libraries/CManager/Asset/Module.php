<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 2:34:11 AM
 */
class CManager_Asset_Module {
    protected static $instance;

    protected $runTimeModules = [];

    protected $themeModules = [];

    protected $unregisteredThemeModules = [];

    private $allModules = null;

    public function __construct() {
        $this->allModules = null;
        $this->reset();
        $this->loadUnregisteredThemeModules();
    }

    public function reset() {
        $this->runTimeModules = [];
        $this->themeModules = [];
        $this->unregisteredThemeModules = [];
    }

    public function allModules() {
        if ($this->allModules == null) {
            $this->allModules = [];
            $clientModulesFiles = CF::getFiles('config', 'client_modules');

            //$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
            $clientModulesFiles = array_reverse($clientModulesFiles);

            foreach ($clientModulesFiles as $file) {
                $appModules = include $file;
                if (!is_array($appModules)) {
                    throw new CManager_Exception('Invalid client modules config format on :file', [':file', $file]);
                }

                $this->allModules = array_merge($this->allModules, $appModules);
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
        $inArray = in_array($mod, $this->runTimeModules);
        if (!$inArray) {
            $inArray = in_array($mod, $this->themeModules);
        }
        if (!$inArray) {
            $inArray = in_array($mod, $this->unregisteredThemeModules);
        }

        return $inArray;
    }

    /**
     * @return array
     */
    public function getRegisteredModule() {
        return array_merge($this->themeModules, $this->runTimeModules);
    }

    public function walkerCallback($tree, $node, $text) {
        if (is_array($text)) {
            $text = implode(',', $text);
        }
        return $text;
    }

    public function registerRunTimeModules($modules) {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        foreach ($modules as $module) {
            $this->registerRunTimeModule($module);
        }
    }

    public function registerThemeModules($modules) {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        foreach ($modules as $module) {
            $this->registerThemeModule($module);
        }
    }

    public function defineModule($name, $moduleData) {
        //make sure all modules is collected
        $this->allModules();
        //replace or make new module
        $this->allModules[$name] = $moduleData;
        return $this->allModules;
    }

    public function unregisterRunTimeModule($module) {
        if (isset($this->runTimeModules[$module])) {
            unset($this->runTimeModules[$module]);
        }
    }

    public function unregisterThemeModule($module) {
        if (isset($this->themeModules[$module])) {
            unset($this->themeModules[$module]);
        }
    }

    public function registerRunTimeModule($module) {
        $allModules = $this->allModules();
        if (!in_array($module, $this->runTimeModules)) {
            if (!isset($allModules[$module])) {
                throw new CManager_Exception('Module :module not defined', [':module' => $module]);
            }
            //array
            $mod = $allModules[$module];
            if (isset($mod['requirements'])) {
                foreach ($mod['requirements'] as $req) {
                    $this->registerRunTimeModule($req);
                }
            }
            if (!in_array($module, $this->runTimeModules)) {
                $this->runTimeModules[] = $module;
            }
        }

        return true;
    }

    public function registerThemeModule($module) {
        $allModules = $this->allModules();
        if (!in_array($module, $this->themeModules)) {
            if (!isset($allModules[$module])) {
                throw new CManager_Exception('Module :module not defined', [':module' => $module]);
            }
            //array
            $mod = $allModules[$module];
            if (isset($mod['requirements'])) {
                foreach ($mod['requirements'] as $req) {
                    $this->registerThemeModule($req);
                }
            }
            if (!in_array($module, $this->themeModules)) {
                $this->themeModules[] = $module;
            }
        }

        return true;
    }

    public function getRunTimeContainer() {
        $runTimeContainer = new CManager_Asset_Container_RunTime();
        $allModules = $this->allModules();

        foreach ($this->runTimeModules as $runTimeModule) {
            $mod = carr::get($allModules, $runTimeModule, []);

            if (isset($mod['js'])) {
                $runTimeContainer->registerJsFiles($mod['js']);
            }
            if (isset($mod['css'])) {
                $runTimeContainer->registerCssFiles($mod['css']);
            }
        }

        return $runTimeContainer;
    }

    public function getThemeContainer() {
        $themeContainer = new CManager_Asset_Container_Theme();
        $allModules = $this->allModules();
        foreach ($this->themeModules as $themeModule) {
            $mod = carr::get($allModules, $themeModule, []);
            if (isset($mod['js'])) {
                $themeContainer->registerJsFiles($mod['js']);
            }
            if (isset($mod['css'])) {
                $themeContainer->registerCssFiles($mod['css']);
            }
        }

        return $themeContainer;
    }

    /**
     * @return CClientModules
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
}