<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CManager {
    use CTrait_Compat_Manager;

    protected $controls = [];

    protected $controlsCode = [];

    protected $elements = [];

    protected $elements_code = [];

    /**
     * @var CManager_GoogleFonts
     */
    protected $googleFonts = null;

    protected static $langObjectCallback = null;

    protected static $useRequireJs = false;

    /**
     * @var CManager_Javascript
     */
    protected static $javascript;

    /**
     * @var CManager_Asset
     */
    protected static $asset;

    /**
     * @var CManager_Theme
     */
    protected static $theme = null;

    /**
     * @var CManager_Navigation
     */
    protected static $navigation = null;

    private static $instance;

    /**
     * @return CManager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CManager();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    /**
     * @return CManager_Theme
     */
    public static function theme() {
        if (self::$theme == null) {
            self::$theme = new CManager_Theme();
        }

        return self::$theme;
    }

    /**
     * @return CManager_Navigation
     */
    public static function navigation() {
        if (self::$navigation == null) {
            self::$navigation = new CManager_Navigation();
        }

        return self::$navigation;
    }

    /**
     * @return CClientScript
     *
     * @deprecated since 1.6 use CManager::asset()->runTime()
     */
    public static function clientScript() {
        return CClientScript::instance();
    }

    /**
     * @return CClientModule
     *
     * @deprecated since 1.6 dont use this anymore, use c::manager()->assets->module
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
     * @param string $module
     * @param array  $data   optional
     *
     * @return bool
     */
    public static function registerModule($module, $data = []) {
        if (!empty($data)) {
            CManager::asset()->module()->defineModule($module, $data);
        }
        if (!CManager::asset()->module()->isRegisteredModule($module)) {
            return CManager::asset()->module()->registerRunTimeModule($module);
        }

        return false;
    }

    public static function registerThemeModule($module, $data = []) {
        if (!empty($data)) {
            CManager::asset()->module()->defineModule($module, $data);
        }

        return CManager::asset()->module()->registerThemeModule($module);
    }

    public static function isRegisteredModule($module) {
        return CManager::asset()->module()->isRegisteredModule($module);
    }

    public static function getRegisteredModule() {
        return CManager::asset()->module()->getRegisteredModule();
    }

    /**
     * @param string $module
     *
     * @return bool
     */
    public static function unregisterModule($module) {
        return CManager::asset()->module()->unregisterRunTimeModule($module);
    }

    public function registerControls($controls) {
        foreach ($controls as $type => $class) {
            $this->controls[$type] = $class;
            $this->controlsCode[$type] = '';
        }
    }

    /**
     * @param string $type
     * @param string $class
     * @param string $codePath
     *
     * @throws Exception
     *
     * @return bool
     */
    public function registerControl($type, $class, $codePath = '') {
        $this->controls[$type] = $class;
        $this->controlsCode[$type] = $codePath;
        if (strlen($codePath) > 0) {
            if (file_exists($codePath)) {
                include $codePath;
            } else {
                throw new Exception(c::__('File :code_path not exists', [':code_path' => $code_path]));
            }
        }

        return true;
    }

    /**
     * @param string $type
     * @param string $class
     * @param string $code_path optional
     *
     * @throws Exception
     *
     * @return bool true if no error
     */
    public function registerElement($type, $class, $code_path = '') {
        $this->elements[$type] = $class;
        $this->elements_code[$type] = $code_path;
        if (strlen($code_path) > 0) {
            if (file_exists($code_path)) {
                include $code_path;
            } else {
                throw new Exception(c::__('File :code_path not exists', [':code_path' => $code_path]));
            }
        }

        return true;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isRegisteredControl($type) {
        return isset($this->controls[$type]);
    }

    /**
     * @return array
     */
    public function getRegisteredControls() {
        return $this->controls;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isRegisteredElement($type) {
        return isset($this->elements[$type]);
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @throws CException
     *
     * @return CElement_FormInput
     */
    public function createControl($id, $type) {
        $class = null;

        if (isset($this->controls[$type])) {
            $class = $this->controls[$type];
        }

        if ($class == null) {
            if (class_exists($type)) {
                $class = $type;
            }
        }
        if ($class == null) {
            //check for class exists
            throw new Exception(c::__('Type of control :type not registered', [':type' => $type]));
        }
        if (cstr::startsWith($class, 'CElement_FormInput')) {
            return CElement_Factory::createFormInput($class, $id);
        }

        return call_user_func([$class, 'factory'], ($id));
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @throws Exception
     *
     * @return CElement_Element
     */
    public function createElement($id, $type) {
        if (!isset($this->elements[$type])) {
            throw new Exception(c::__('Type of element :type not registered', [':type' => $type]));
        }
        $class = $this->elements[$type];

        if (cstr::startsWith($class, 'CElement_Element')) {
            return CElement_Factory::createElement($id);
        }

        return call_user_func([$class, 'factory'], ($id));
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

    public static function registerTransform($method, callable $callback) {
        $transformManager = CManager_Transform::instance();

        return $transformManager->addCallback($method, $callback);
    }

    public static function addTransformCallback($method, callable $callback) {
        return static::registerTransform($method, $callback);
    }

    /**
     * @param bool $bool
     *
     * @return void
     *
     * @deprecated since 1.2
     */
    public static function setUseRequireJs($bool) {
        self::$useRequireJs = $bool;
    }

    public static function getUseRequireJs() {
        if (CApp::isAjax()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $file
     * @param string $pos
     *
     * @return CManager_Asset_Container
     */
    public static function registerCss($file, $pos = CManager_Asset::POS_HEAD) {
        return CManager::asset()->runTime()->registerCssFile($file, $pos);
    }

    /**
     * @param string|array $file
     * @param string       $pos
     *
     * @return CManager_Asset_Container
     */
    public static function registerJs($file, $pos = CManager_Asset::POS_END) {
        return CManager::asset()->runTime()->registerJsFile($file, $pos);
    }

    /**
     * @return CManager_Javascript
     */
    public static function javascript() {
        if (self::$javascript == null) {
            self::$javascript = new CManager_Javascript();
        }

        return self::$javascript;
    }

    /**
     * @return CManager_Asset
     */
    public static function asset() {
        if (self::$asset == null) {
            self::$asset = new CManager_Asset();
        }

        return self::$asset;
    }

    public static function registerDaemon($class, $name = null, $group = null) {
        return CManager_Daemon::instance()->registerDaemon($class, $name, $group);
    }

    public static function getRegisteredDaemon() {
        return CManager_Daemon::instance()->daemons();
    }

    /**
     * Get Daemon Manager.
     *
     * @return CManager_Daemon
     */
    public static function daemon() {
        return CManager_Daemon::instance();
    }

    /**
     * Get View Manager.
     *
     * @return CView_Factory
     */
    public static function view() {
        return CView_Factory::instance();
    }

    /**
     * @return CManager_Icon
     */
    public static function icon() {
        return CManager_Icon::instance();
    }

    /**
     * @param string  $model
     * @param Closure $queryCallback
     *
     * @return CManager_DataProvider_ModelDataProvider
     */
    public static function createModelDataProvider($model, $queryCallback = null) {
        return new CManager_DataProvider_ModelDataProvider($model, $queryCallback);
    }

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return CManager_DataProvider_SqlDataProvider
     */
    public static function createSqlDataProvider($sql, array $bindings = []) {
        return new CManager_DataProvider_SqlDataProvider($sql, $bindings);
    }

    /**
     * @param string|array $requires
     * @param Closure      $closure
     *
     * @return CManager_DataProvider_SqlDataProvider
     */
    public static function createClosureDataProvider($closure, $requires = []) {
        return new CManager_DataProvider_ClosureDataProvider($closure, carr::wrap($requires));
    }

    /**
     * @param CCollection $collection
     *
     * @return CManager_DataProvider_CollectionDataProvider
     */
    public static function createCollectionDataProvider(CCollection $collection) {
        return new CManager_DataProvider_CollectionDataProvider($collection);
    }

    /**
     * Get Transform Manager.
     *
     * @return CManager_Transform
     */
    public static function transform() {
        return CManager_Transform::instance();
    }

    /**
     * @return CManager_EditorJs
     */
    public static function editorJs() {
        return CManager_EditorJs::instance();
    }

    public static function onBoarding() {
        return CManager_OnBoarding_Steps::instance();
    }

    /**
     * @return CManager_Factory
     */
    public static function factory() {
        return new CBase_ForwarderStaticClass(CManager_Factory::class);
    }

    /**
     * @return CManager_FileProvider_FileProvider
     */
    public static function createFileProvider() {
        return new CManager_FileProvider_FileProvider();
    }

    /**
     * @return CManager_FileProvider_ImageFileProvider
     */
    public static function createImageFileProvider() {
        return new CManager_FileProvider_ImageFileProvider();
    }

    public function googleFonts() {
        if ($this->googleFonts == null) {
            $this->googleFonts = new CManager_GoogleFonts(
                CStorage::instance()->disk(CF::config('assets.google_fonts.disk')),
                CF::config('assets.google_fonts.path'),
                CF::config('assets.google_fonts.inline'),
                CF::config('assets.google_fonts.fallback'),
                CF::config('assets.google_fonts.user_agent'),
                CF::config('assets.google_fonts.fonts'),
                CF::config('assets.google_fonts.preload', false)
            );
        }

        return $this->googleFonts;
    }

    public static function registerBlade() {
        CView::blade()->directive('googlefonts', function ($expression) {
            return "<?php echo c::manager()->googleFonts()->load($expression)->toHtml(); ?>";
        });
    }
}
