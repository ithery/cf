<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CClientModules {

    public static $mods = array();
    public static $all_modules = array();
    protected static $_instance;

    public function __construct() {

        $this->mods = array();
        $this->all_modules = null;
    }

    public function all_modules() {
        if ($this->all_modules == null) {
            $this->all_modules = include DOCROOT . "config" . DS . "client_modules" . DS . "client_modules.php";
            $app_files = CF::get_files('config', 'client_modules');
            //$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
            $app_files = array_reverse($app_files);

            foreach ($app_files as $file) {
                $app_modules = include $file;
                if (!is_array($app_modules)) {
                    trigger_error("Invalid Client Modules Config Format On " . $file);
                }

                $this->all_modules = array_merge($this->all_modules, $app_modules);
            }
        }
        return $this->all_modules;
    }

    public function requirements($module) {
        $data = array();
        $all_modules = $this->all_modules();
        if (isset($all_modules[$module])) {
            $mod = $all_modules[$module];
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

        return in_array($mod, $this->mods);
    }

    private function add_to_tree($tree, $module) {
        $all_modules = $this->all_modules();
        $mod = $all_modules[$module];
        $last_req = null;
        if (isset($mod["requirements"])) {
            foreach ($mod["requirements"] as $req) {
                $this->add_to_tree($tree, $req);
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
            $this->add_to_tree($tree, $mod);
        }
        return $tree;
    }

    public static function walker_callback($tree, $node, $text) {

        if (is_array($text)) {
            $text = implode(",", $text);
        }
        return $text;
    }

    public function require_js($js) {
        $tree = $this->jstree();

        //$tree->set_walker_callback(array('CClientModules','walker_callback'));
        echo $tree->html();
        die();
    }

    public function register_modules($modules) {
        if (!is_array($modules)) {
            $modules = array($modules);
        }
        foreach ($modules as $module) {
            $this->register_module($module);
        }
    }

    public function defineModule($name, $moduleData) {
        //make sure all modules is collected
        $this->all_modules();
        //replace or make new module
        $this->all_modules[$name] = $moduleData;
        return $this;
    }

    public function register_module($module, $parent = null) {
        $cs = CClientScript::instance();

        $all_modules = $this->all_modules();
        if (!in_array($module, $this->mods)) {

            if (isset($all_modules[$module])) {
                //array
                $mod = $all_modules[$module];
                if (isset($mod["requirements"])) {
                    foreach ($mod["requirements"] as $req) {
                        $this->register_module($req);
                    }
                }
                if (!in_array($module, $this->mods)) {

                    if (isset($mod["js"]))
                        $cs->register_js_files($mod["js"]);
                    if (isset($mod["css"]))
                        $cs->register_css_files($mod["css"]);
                    $this->mods[] = $module;
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
