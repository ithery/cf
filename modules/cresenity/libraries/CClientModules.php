<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CClientModules {

    use CTrait_Compat_ClientModules;

    public static $mods = array();
    public static $allModules = array();
    protected static $_instance;

    public function __construct() {

        self::$mods = array();
        self::$allModules = null;
    }

    public function allModules() {
        if (self::$allModules == null) {
            self::$allModules = include DOCROOT . "config" . DS . "client_modules" . DS . "client_modules.php";
            $app_files = CF::get_files('config', 'client_modules');
            //$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
            $app_files = array_reverse($app_files);

            foreach ($app_files as $file) {
                $app_modules = include $file;
                if (!is_array($app_modules)) {
                    trigger_error("Invalid Client Modules Config Format On " . $file);
                }

                self::$allModules = array_merge(self::$allModules, $app_modules);
            }
        }
        return self::$allModules;
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

    public function is_registered_module($mod) {

        return in_array($mod, self::$mods);
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
        if ($tree->get_node($module) == null) {
            if (isset($mod['js'])) {
                $tree->add_child($node, $module, $mod['js']);
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

    public static function walker_callback($tree, $node, $text) {

        if (is_array($text)) {
            $text = implode(",", $text);
        }
        return $text;
    }

    public function requireJs($js) {
        $tree = $this->jstree();

        //$tree->set_walker_callback(array('CClientModules','walker_callback'));
        echo $tree->html();
        die();
    }

    public function registerModules($modules) {
        if (!is_array($modules)) {
            $modules = array($modules);
        }
        foreach ($modules as $module) {
            $this->register_module($module);
        }
    }

    public function defineModule($name, $moduleData) {
        //make sure all modules is collected
        $this->allModules();
        //replace or make new module
        self::$allModules[$name] = $moduleData;
        return $this;
    }

    public function unregisterModule($module) {
        $cs = CClientScript::instance();

        $allModules = $this->allModules();

        if (in_array($module, self::$mods)) {
            //locate mods

            foreach (self::$mods as $mod) {

                if ($mod == $module) {

                    //mod found, we need locate js and css files from allModules
                    $moduleData = carr::get($allModules, $module);
                    $jsFiles = carr::get($moduleData, 'js');
                    $cssFiles = carr::get($moduleData, 'css');
                    $cs->unregisterJsFiles($jsFiles);
                    $cs->unregisterCssFiles($cssFiles);
                }
            }
        }
        return false;
    }

    public function registerModule($module, $parent = null) {
        $cs = CClientScript::instance();

        $allModules = $this->allModules();
        if (!in_array($module, self::$mods)) {

            if (isset($allModules[$module])) {
                //array
                $mod = $allModules[$module];
                if (isset($mod["requirements"])) {
                    foreach ($mod["requirements"] as $req) {
                        $this->register_module($req);
                    }
                }
                if (!in_array($module, self::$mods)) {

                    if (isset($mod["js"]))
                        $cs->register_js_files($mod["js"]);
                    if (isset($mod["css"]))
                        $cs->register_css_files($mod["css"]);
                    self::$mods[] = $module;
                }
            } else {

                trigger_error('Module ' . $module . ' not defined');
            }
        }
        return true;
    }

    /**
     * 
     * @return CClientModules
     */
    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CClientModules();
        }
        return self::$_instance;
    }

}
