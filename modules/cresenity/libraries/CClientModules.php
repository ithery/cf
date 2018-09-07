<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CClientModules {

    use CTrait_Compat_ClientModules;

    protected static $_instance;

    public function allModules() {
        return CManager::asset()->module()->allModules();
    }

    public function requirements($module) {
        return CManager::asset()->module()->requirements($module);
    }

    public function isRegisteredModule($mod) {
        return CManager::asset()->module()->isRegisteredModule($mod);
    }

    public function jstree() {
        return CManager::asset()->module()->jstree($module);
    }

    public function registerModules($modules) {
        return CManager::asset()->module()->registerRunTimeModules($modules);
    }

    public function defineModule($name, $moduleData) {
        return CManager::asset()->module()->defineModule($name, $moduleData);
    }

    public function unregisterModule($module) {
        return CManager::asset()->module()->unregisterRunTimeModule($module);
    }

    public function registerModule($module, $parent = null) {
        return CManager::asset()->module()->registerRunTimeModule($module);
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
