<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 2:34:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Asset_Module {

    protected static $instance;
    protected $runTimeModules = array();
    protected $themeModules = array();
    private $allModules = null;

    public function __construct() {
        $this->allModules = null;
        $this->reset();
    }

    public function reset() {
        $this->runTimeModules = array();
        $this->themeModules = array();
    }

    public function allModules() {
        if ($this->allModules == null) {
            $this->allModules = include DOCROOT . "config" . DS . "client_modules" . DS . "client_modules.php";
            $app_files = CF::get_files('config', 'client_modules');
            //$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
            $app_files = array_reverse($app_files);

            foreach ($app_files as $file) {
                $appModules = include $file;
                if (!is_array($appModules)) {
                    throw new CManager_Exception("Invalid client modules config format on :file", array(':file', $file));
                }

                $this->allModules = array_merge($this->allModules, $appModules);
            }
        }
        return $this->allModules;
    }

    public function requirements($module) {
        $data = array();
        $allModules = $this->allModules();
        if (isset($allModules[$module])) {
            $mod = $allModules[$module];
            if (isset($mod["requirements"])) {
                foreach ($mod["requirements"] as $req) {
                    $data_req = $this->requirements($req);
                    $data[] = $req;
                    $data = array_merge($data_req, $data);
                }
            }
        }
        return $data;
    }

    /**
     * 
     * @param string $mod
     * @return bool
     */
    public function isRegisteredModule($mod) {
        $inArray = in_array($mod, $this->runTimeModules);
        if (!$inArray) {
            $inArray = in_array($mod, $this->themeModules);
        }
        return $inArray;
    }

    /**
     * 
     * @return array
     */
    public function getRegisteredModule() {
        return array_merge($this->themeModules, $this->runTimeModules);
    }

    private function addToTree($tree, $module) {
        $allModules = $this->allModules();
        $mod = $allModules[$module];
        $last_req = null;
        if (isset($mod["requirements"])) {
            foreach ($mod["requirements"] as $req) {
                $this->addToTree($tree, $req);
                $last_req = $req;
            }
        }
        $node = $tree->root();
        if ($last_req != null) {
            $node = $tree->get_node($last_req);
        }
        if ($tree->getNode($module) == null) {
            if (isset($mod['js'])) {
                $tree->addChild($node, $module, $mod['js']);
            }
        }
    }

    public function jstree() {
        $tree = CTree::factory('root', null);

        foreach ($this->mods as $mod) {
            $this->addToTree($tree, $mod);
        }
        return $tree;
    }

    public function walkerCallback($tree, $node, $text) {

        if (is_array($text)) {
            $text = implode(",", $text);
        }
        return $text;
    }

    public function registerRunTimeModules($modules) {
        if (!is_array($modules)) {
            $modules = array($modules);
        }
        foreach ($modules as $module) {
            $this->registerRunTimeModule($module);
        }
    }

    public function registerThemeModules($modules) {
        if (!is_array($modules)) {
            $modules = array($modules);
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
        $allModules = self::allModules();
        if (!in_array($module, $this->runTimeModules)) {
            if (!isset($allModules[$module])) {
                throw new CManager_Exception('Module :module not defined', array(':module' => $module));
            }
            //array
            $mod = $allModules[$module];
            if (isset($mod["requirements"])) {
                foreach ($mod["requirements"] as $req) {
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
        $allModules = self::allModules();
        if (!in_array($module, $this->themeModules)) {
            if (!isset($allModules[$module])) {
                throw new CManager_Exception('Module :module not defined', array(':module' => $module));
            }
            //array
            $mod = $allModules[$module];
            if (isset($mod["requirements"])) {
                foreach ($mod["requirements"] as $req) {
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
            $mod = carr::get($allModules, $runTimeModule, array());
          
            if (isset($mod["js"])) {
                $runTimeContainer->registerJsFiles($mod['js']);
            }
            if (isset($mod["css"])) {
                $runTimeContainer->registerCssFiles($mod['css']);
            }
        }

        return $runTimeContainer;
    }

    public function getThemeContainer() {
        $themeContainer = new CManager_Asset_Container_Theme();
        $allModules = $this->allModules();
        foreach ($this->themeModules as $themeModule) {
            $mod = carr::get($allModules, $themeModule, array());
            if (isset($mod["js"])) {
                $themeContainer->registerJsFiles($mod['js']);
            }
            if (isset($mod["css"])) {
                $themeContainer->registerCssFiles($mod['css']);
            }
        }

        return $themeContainer;
    }

    /**
     * 
     * @return CClientModules
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CManager_Asset_Module();
        }
        return self::$instance;
    }

}
