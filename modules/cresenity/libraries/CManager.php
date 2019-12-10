<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CManager {

    use CTrait_Compat_Manager;

    private static $_instance;
    protected $controls = array();
    protected $controls_code = array();
    protected $elements = array();
    protected $elements_code = array();
    protected $is_mobile = false;
    protected $mobile_path = '';
    protected $theme_data = null;
    protected static $langObjectCallback = null;
    protected static $useRequireJs = null;

    /**
     *
     * @var CManager_Javascript
     */
    protected static $javascript;

    /**
     *
     * @var CManager_Asset
     */
    protected static $asset;

    /**
     *
     * @var CManager_Theme
     */
    protected static $theme = null;

    /**
     *
     * @var CManager_Navigation
     */
    protected static $navigation = null;

    /**
     *
     * @return CManager
     */
    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CManager();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->is_mobile = ccfg::get('is_mobile');
        $this->mobile_path = '';

//            $theme = ccfg::get('theme');
//            if ($theme == null) $theme = 'cresenity';
        $theme = ctheme::get_current_theme();
        $theme_file = CF::get_file('themes', $theme);
        if (file_exists($theme_file)) {
            $this->theme_data = include $theme_file;
        }
        self::theme();
    }

    /**
     * 
     * @return CManager_Theme
     */
    public static function theme() {
        if (self::$theme == null) {
            self::$theme = new CManager_Theme();
        }
        return self::$theme;
    }

    /**
     * 
     * @return CManager_Navigation
     */
    public static function navigation() {
        if (self::$navigation == null) {
            self::$navigation = new CManager_Navigation();
        }
        return self::$navigation;
    }

    /**
     * 
     * @return CClientScript
     */
    public static function clientScript() {
        return CClientScript::instance();
    }

    /**
     * 
     * @return CClientModule
     */
    public static function clientModule() {
        return CClientModules::instance();
    }

    public function getThemeData() {
        return $this->theme()->getThemeData();
    }

    public function setThemeData($themeData) {
        $this->theme()->setThemeData($themeData);
        return $this;
    }

    /**
     * 
     * @param string $module
     * @param array $data optional
     * @return boolean
     */
    public static function registerModule($module, $data = array()) {
        if (!empty($data)) {
            CClientModules::instance()->defineModule($module, $data);
        }
        if (!CClientModules::instance()->isRegisteredModule($module)) {
            return CClientModules::instance()->registerModule($module);
        }
        return false;
    }

    public static function registerThemeModule($module, $data = array()) {
        if (!empty($data)) {
            CClientModules::instance()->defineModule($module, $data);
        }
        return CClientModules::instance()->registerThemeModule($module);
    }

    public static function isRegisteredModule($module) {
        return CClientModules::instance()->isRegisteredModule($module);
    }

    public static function getRegisteredModule() {
        return CClientModules::instance()->getRegisteredModule();
    }

    /**
     * 
     * @param string $module
     * @return boolean
     */
    public static function unregisterModule($module) {
        return CClientModules::instance()->unregisterModule($module);
    }

    /**
     * backward compatibility of registerModule
     * 
     * @param string $module
     * @param array $data optional
     * @return boolean
     */
    public static function register_module($module, $data = array()) {
        return self::registerModule($module, $data);
    }

    public static function unregister_module($module) {
        return CClientModules::instance()->unregister_module($module);
    }

    /**
     * 
     * @param string $type
     * @param string $class
     * @param string $code_path
     * @return boolean
     * @throws CException
     */
    public function registerControl($type, $class, $code_path = '') {
        $this->controls[$type] = $class;
        $this->controls_code[$type] = $code_path;
        if (strlen($code_path) > 0) {
            if (file_exists($code_path)) {
                include $code_path;
            } else {
                throw new CException('File :code_path not exists', array(':code_path' => $code_path));
            }
        }
        return true;
    }

    /**
     * 
     * @param string $type
     * @param string $class
     * @param string $code_path optional
     * @return boolean true if no error
     * @throws CException 
     */
    public function registerElement($type, $class, $code_path = '') {
        $this->elements[$type] = $class;
        $this->elements_code[$type] = $code_path;
        if (strlen($code_path) > 0) {
            if (file_exists($code_path)) {
                include $code_path;
            } else {
                throw new CException('File :code_path not exists', array(':code_path' => $code_path));
            }
        }
        return true;
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public function isRegisteredControl($type) {
        return isset($this->controls[$type]);
    }

    /**
     * 
     * @return array
     */
    public static function getRegisteredControls() {
        return $this->controls;
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public function isRegisteredElement($type) {
        return isset($this->elements[$type]);
    }

    /**
     * 
     * @param string $id
     * @param string $type
     * @return CElement_FormInput
     * @throws CException
     */
    public function createControl($id, $type) {

        if (!isset($this->controls[$type])) {
            throw new CException('Type of control :type not registered', array(':type' => $type));
        }
        $class = $this->controls[$type];

        if (cstr::startsWith($class, 'CElement_FormInput')) {
            return CElement_Factory::createFormInput($class, $id);
        }
        return call_user_func(array($class, 'factory'), ($id));
    }

    /**
     * 
     * @param string $id
     * @param string $type
     * @return CElement_Element
     * @throws CException
     */
    public function createElement($id, $type) {
        if (!isset($this->elements[$type])) {
            throw new CException('Type of element :type not registered', array(':type' => $type));
        }
        $class = $this->elements[$type];

        if (cstr::startsWith($class, 'CElement_Element')) {
            return CElement_Factory::createElement($id);
        }
        return call_user_func(array($class, 'factory'), ($id));
    }

    /**
     * 
     * @param type $path
     * @return $this
     */
    public function setMobilePath($path) {
        $this->mobile_path = $path;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getMobilePath() {
        return $this->mobile_path;
    }

    public function isMobile() {
        return $this->is_mobile;
    }

    public static function lang() {
        if (self::$langObjectCallback != null) {
            return call_user_func(self::$langObjectCallback);
        }
        return new CManager_Lang();
    }

    public static function setLangObjectCallback(callable $callback) {
        self::$langObjectCallback = $callback;
    }

    public static function addTransformCallback($method, callable $callback) {
        $transformManager = CManager_Transform::instance();
        return $transformManager->addCallback($method, $callback);
    }

    public static function setUseRequireJs($bool) {
        self::$useRequireJs = $bool;
    }

    public static function getUseRequireJs() {
        return true;
        if (self::$useRequireJs === null) {
            $require = ccfg::get('require_js');
            if ($require === null) {
                return true;
            }
            return $require;
        }
        return self::$useRequireJs;
    }

    public static function registerCss($file, $pos = CClientScript::POS_HEAD) {
        $cs = CClientScript::instance()->registerCssFile($file, $pos);
    }

    public static function registerJs($file, $pos = CClientScript::POS_END) {
        $cs = CClientScript::instance()->registerJsFile($file, $pos);
    }

    /**
     * 
     * @return CManager_Javascript
     */
    public static function javascript() {
        if (self::$javascript == null) {
            self::$javascript = new CManager_Javascript();
        }
        return self::$javascript;
    }

    /**
     * 
     * @return CManager_Asset
     */
    public static function asset() {
        if (self::$asset == null) {

            self::$asset = new CManager_Asset();
        }
        return self::$asset;
    }

    
    public static function registerDaemon($class) {
        return CManager_Daemon::instance()->registerDaemon($class);
    }
    
    public static function registeredDaemon() {
        return CManager_Daemon::instance()->daemons();
    }
}
