<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CApp extends CObservable {

    use CTrait_Compat_App,
        CApp_Trait_App_Breadcrumb,
        CApp_Trait_App_Renderer,
        CApp_Trait_App_Auth,
        CApp_Trait_App_Title;

    private $content = "";
    private $js = "";
    private $custom_js = "";
    private $custom_header = "";
    private $custom_footer = "";
    private $custom_data = array();
    private $signup = false;
    private $activation = false;
    private $resend = false;
    private $_store = null;
    private $_org = null;
    private $_admin = null;
    private $_member = null;
    public static $_instance = null;
    protected $rendered = false;
    private $header_body = '';
    private $additional_head = '';
    private $ajaxData = array();
    private $renderMessage = true;
    private $keepMessage = false;
    private $viewName = 'cpage';
    private $viewLoginName = 'ccore/login';
    protected static $viewCallback;

    public function setViewCallback(callable $viewCallback) {
        self::$viewCallback = $viewCallback;
    }

    /**
     * 
     * @param string $domain
     * @return CApp_Navigation
     */
    public static function navigation($domain = null) {
        return CApp_Navigation::instance($domain);
    }

    /**
     * 
     * @param string $domain
     * @return CApp_Api
     */
    public static function api($domain = null) {
        return CApp_Api::instance($domain);
    }

    /**
     * 
     * @param string $domain
     * @return CApp_Remote
     */
    public static function remote($domain = null, $options = array()) {
        return CApp_Remote::instance($domain, $options);
    }

    /**
     * 
     * @param string $modelName
     * @return CApp_Model
     */
    public static function model($modelName) {
        $modelClass = 'CApp_Model_' . $modelName;
        return new $modelClass();
    }

    /**
     * 
     * @param string $domain
     * @return CApp_Navigation
     */
    public static function nav($domain = null) {
        return CApp_Navigation::instance($domain);
    }

    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * 
     * @return CApp_Temp
     */
    public static function temp() {
        return new CApp_Temp();
    }

    /**
     * 
     * @return CApp_Data
     */
    public static function data() {
        return new CApp_Data();
    }

    public static function getTranslation($message, $params = array(), $lang = null) {
        return CApp_Lang::__($message, $params, $lang);
    }

    /**
     * 
     * @return CDatabase
     */
    public static function db($domain = null, $dbName = null) {
        return CDatabase::instance($domain, $dbName);
    }

    public function setAjaxData($key, $value = null) {
        if (is_array($key)) {
            $this->ajaxData = array_merge($this->ajaxData, $key);
        } else {
            $this->ajaxData[$key] = $value;
        }
        return $this;
    }

    public function setRenderMessage($bool) {
        $this->renderMessage = $bool;
    }

    public function setKeepMessage($bool) {
        $this->keepMessage = $bool;
    }

    public function translator() {
        return new CApp_Translation_Translator(new CApp_Translation_Loader_ArrayLoader(), 'en');
    }

    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        $translator = $this->translator();
        $validation = new CApp_Validation($translator);
        return $validation->validate($data, $rules, $messages, $customAttributes);
    }

    public function __construct($domain = null) {

        parent::__construct();


        $this->_org = corg::get(CF::orgCode());


        if (isset($_COOKIE['capp-profiler'])) {
            new Profiler();
        }

        $db = CDatabase::instance();

        if (isset($_COOKIE['capp-debugbar'])) {
            CDebug::bar()->enable();
        }
        //we load another configuration for this app
        //org configuration
        if (strlen(CF::orgCode()) > 0) {
            $orgBootFile = DOCROOT . "application" . DS . $this->code() . DS . CF::orgCode() . DS . CF::orgCode() . EXT;
            if (file_exists($orgBootFile)) {
                include($orgBootFile);
            }
        }


        $appBootFile = DOCROOT . "application" . DS . $this->code() . DS . $this->code() . EXT;

        if (file_exists($appBootFile)) {
            include($appBootFile);
        }


        $org = $this->org();

        if (ccfg::get("set_timezone")) {
            $timezone = ccfg::get("default_timezone");
            if ($org != null) {
                //$timezone = $org->timezone;
            }

            date_default_timezone_set($timezone);
        }

        $this->id = "capp";
        //check login


        if (ccfg::get("update_last_request")) {
            $user = $this->user();
            if ($user != null) {
                if (!is_array($user)) {
                    //update last request
                    $db = $this->db();
                    $db->update("users", array("last_request" => date("Y-m-d H:i:s")), array("user_id" => $user->user_id));
                }
            }
        }
    }

    public function appId() {
        return CF::appId();
    }

    public function manager() {
        return CManager::instance();
    }

    public function name() {
        //$app = CJDB::instance()->get("app", array("app_id" => $this->app_id()));
        //return $app[0]->name;
//		return CF::app_name();
    }

    public function code() {
        return CF::appCode();
    }

    public function message($type, $message) {
        return CApp_Message::add($type, $message);
    }

    public function controller() {
        return CF::instance();
    }

    public static function config($path, $domain = null) {
        return CApp_Config::get($path, $domain);
    }

    public function set_header_body($header_body) {
        $this->header_body = $header_body;
        return $this;
    }

    public function isAdmin() {
        return $this->appId() == 0;
    }

    /**
     * 
     * @param boolean $install
     * @return CApp
     */
    public static function factory($install = false) {
        //return new CApp($install);
        return self::instance($install);
    }

    /**
     * 
     * @param boolean $install
     * @return CApp
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (self::$_instance == null) {
            self::$_instance = array();
        }
        if (!isset(self::$_instance[$domain])) {
            self::$_instance[$domain] = new CApp($domain);
        }
        return self::$_instance[$domain];
    }

    public function signup($bool = true) {
        $this->signup = $bool;
        return $this;
    }

    public function resend($bool = true) {
        $this->resend = $bool;
        return $this;
    }

    public function activation($bool = true) {
        $this->activation = $bool;
        return $this;
    }

    public function addCustomJs($js) {
        $this->custom_js .= $js;
        return $this;
    }

    public function set_view_html() {
        
    }

    public function registerCoreModules() {
        $manager = CManager::instance();
        $theme = CManager::theme()->getCurrentTheme();
        $themeFile = CF::getFile('themes', $theme);
        if (file_exists($themeFile)) {
            $themeData = include $themeFile;
            $moduleArray = carr::get($themeData, 'client_modules');
            $cssArray = carr::get($themeData, 'css');
            $jsArray = carr::get($themeData, 'js');
            $cs = CClientScript::instance();
            if ($moduleArray != null) {
                foreach ($moduleArray as $module) {
                    $manager->registerThemeModule($module);
                }
            }
            if (ccfg::get('have_clock')) {
                $manager->registerModule('servertime');
            }
            if ($cssArray != null) {
                foreach ($cssArray as $css) {
                    $manager->asset()->theme()->registerCssFile($css);
                }
            }
            if ($jsArray != null) {
                foreach ($jsArray as $js) {
                    $manager->asset()->theme()->registerJsFiles($js);
                }
            }
        }

        $manager->registerThemeModule('block-ui');
    }

    public function set_additional_head($str) {
        $this->additional_head = $str;
    }

    public function rendered() {
        return $this->rendered;
    }

    public function render() {

        if ($this->rendered) {
            throw new CException('CApp already rendered');
        }
        $this->rendered = true;

        $this->registerCoreModules();


        CFEvent::run('CApp.beforeRender');

        if (crequest::is_ajax()) {
            return $this->json();
        }
        $v = null;

        $viewName = $this->viewName;
        if (ccfg::get("install")) {
            $viewName = 'cinstall/page';
        } else if ($this->signup) {
            $viewName = 'ccore/signup';
        } else if ($this->resend) {
            $viewName = 'ccore/resend_activation';
        } else if ($this->activation) {
            $viewName = 'ccore/activation';
        } else if (!$this->isUserLogin() && $this->config("have_user_login") && $this->loginRequired) {
            $viewName = $this->viewLoginName;
        } else if (!$this->isUserLogin() && $this->config("have_static_login") && $this->loginRequired) {
            $viewName = 'ccore/static_login';
        }

        if (self::$viewCallback != null && is_callable(self::$viewCallback)) {
            $viewName = self::$viewCallback($viewName);
        }

        $themePath = CManager::theme()->getThemePath();

        if (CView::exists($themePath . $viewName)) {
            $v = CView::factory($themePath . $viewName);
        }
        if ($v == null) {
            if (!CView::exists($viewName)) {
                throw new CApp_Exception('view :viewName not exists', array(':viewName' => $viewName));
            }
            $v = CView::factory($viewName);
        }


        $viewData = $this->getViewData();
        $v->set($viewData);



        return $v->render();
    }

    public function setCustomData($data) {
        $this->custom_data = $data;
        return $this;
    }

    public function get_all_js() {
        $cs = CClientScript::instance();
        $this->js = parent::js();
        $additional_js = '';
        $js = "";
        $vjs = CView::factory('ccore/js');
        $js .= PHP_EOL . $vjs->render();

        $js .= PHP_EOL . $this->js . $additional_js;

        $js = $cs->renderJsRequire($js);

        if (ccfg::get("minify_js")) {
            $js = CJSMin::minify($js);
        }

        return $js;
    }

    public function admin() {
        if ($this->_admin == null) {
            $session = CSession::instance();
            $admin = $session->get("admin");
            if (!$admin)
                $admin = null;
            $this->_admin = $admin;
        }
        return $this->_admin;
    }

    public function member() {
        if ($this->_member = null) {
            $session = CSession::instance();
            $member = $session->get("member");
            if (!$member)
                $member = null;
            $this->_member = $member;
        }
        return $this->_member;
    }

    public function org() {

        if ($this->_org == null) {
            $role = $this->role();
            if ($role != null) {
                $this->_org = corg::get($role->org_id);
            }
        }
        return $this->_org;
    }

    public function orgId() {
        $org = $this->org();
        if ($org == null)
            return null;
        return $org->org_id;
    }

    public function is_admin_login() {
        return $this->admin() != null;
    }

    public function is_member_login() {
        return $this->member() != null;
    }

    public function store() {
        if ($this->_store == null) {
            $store_id = CF::store_id();

            if ($store_id != "") {
                $this->_store = cstore::get(CF::orgCode(), CF::store_code());
            }
        }
        return $this->_store;
    }

    public function store_id() {
        $store = $this->store();
        if ($store == null)
            return null;
        return $store->store_id;
    }

    public function getRoleChildList($roleId = null, $orgId = null) {
        if (strlen($roleId) == 0) {
            $roleId = $this->role()->role_id;
        }
        if (strlen($orgId) == 0) {
            $orgId = CApp_Base::orgId();
        }

        $nodes = self::model('Roles')->getDescendantsTree($roleId, $orgId);
        $childList = array();

        $traverse = function ($childs) use (&$traverse, &$childList) {
            foreach ($childs as $child) {

                $depth = carr::get($child, 'depth');
                $childList[$child["role_id"]] = cutils::indent($depth, "&nbsp;&nbsp;") . $child["name"];
                $traverse($child->getChildren);
            }
        };

        $traverse($nodes);

        return $childList;
    }

    public static function exceptionHandler($exception, $message = NULL, $file = NULL, $line = NULL) {

        try {
            $app = CApp::instance();
            $org = $app->org();

            // PHP errors have 5 args, always
            $PHP_ERROR = (func_num_args() === 5);

            // Test to see if errors should be displayed
            if ($PHP_ERROR AND ( error_reporting() & $exception) === 0)
                return;

            // Error handling will use exactly 5 args, every time
            if ($PHP_ERROR) {
                $code = $exception;
                $type = 'PHP Error';
            } else {
                $code = $exception->getCode();
                $type = get_class($exception);
                $message = $exception->getMessage();
                $file = $exception->getFile();
                $line = $exception->getLine();
            }

            if (is_numeric($code)) {
                $codes = CF::lang('errors');

                if (!empty($codes[$code])) {
                    list($level, $error, $description) = $codes[$code];
                } else {
                    $level = 1;
                    $error = $PHP_ERROR ? 'Unknown Error' : get_class($exception);
                    $description = '';
                }
            } else {
                // Custom error message, this will never be logged
                $level = 5;
                $error = $code;
                $description = '';
            }

            // Remove the DOCROOT from the path, as a security precaution
            $file = str_replace('\\', '/', realpath($file));
            $file = preg_replace('|^' . preg_quote(DOCROOT) . '|', '', $file);


            if ($PHP_ERROR) {
                $description = CF::lang('errors.' . E_RECOVERABLE_ERROR);
                $description = is_array($description) ? $description[2] : '';
            }


            // Test if display_errors is on
            $trace = false;
            $traceArray = false;
            if ($line != FALSE) {
                // Remove the first entry of debug_backtrace(), it is the exception_handler call
                $traceArray = $PHP_ERROR ? array_slice(debug_backtrace(), 1) : $exception->getTrace();

                // Beautify backtrace
                $trace = CF::backtrace($traceArray);
            }

            if (!($exception instanceof CF_404_Exception)) {
                $v = CView::factory('cmail/error_mail');
                $v->error = $error;
                $v->description = $description;
                $v->file = $file;
                $v->line = $line;
                $v->trace = $trace;
                $v->message = $message;
                $html = $v->render();
                $configCollector = CConfig::instance('collector');
                if ($configCollector->get('exception')) {
                    if ($PHP_ERROR) {
                        CCollector::error($exception, $message, $file, $line, '');
                    } else {
                        CCollector::exception($exception);
                    }
                } else {
                    cmail::error_mail($html);
                }
            }


            if ($PHP_ERROR) {
                CF::exception_handler($exception, $message, $file, $line, '');
            } else {
                CF::exception_handler($exception, $message, $file, $line);
            }
        } catch (Exception $e) {
            CF::exception_handler($exception, $message, $file, $line);
        }
    }

    //override function json
    public function json() {
        $data = array();
        $data["title"] = $this->title;
        $message = '';
        $messageOrig = '';
        if (!$this->keepMessage) {
            $messageOrig = cmsg::flash_all();
            if ($this->renderMessage) {
                $message = $messageOrig;
            }
        }
        $data["html"] = $message . $this->html();
        $asset = CManager::asset();
        $js = $this->js();
        $js = $asset->renderJsRequire($js);
        if (ccfg::get("minify_js")) {
            $js = CJSMin::minify($js);
        }
        $data["js"] = cbase64::encode($js);
        $data["css_require"] = $asset->getAllCssFileUrl();
        $data["message"] = $messageOrig;
        $data["ajaxData"] = $this->ajaxData;
        return cjson::encode($data);
    }

    public static function variables() {
        $variables = array();
        $variables['decimal_separator'] = ccfg::get('decimal_separator') === null ? '.' : ccfg::get('decimal_separator');
        $variables['decimalSeparator'] = ccfg::get('decimal_separator') === null ? '.' : ccfg::get('decimal_separator');
        $variables['thousand_separator'] = ccfg::get('thousand_separator') === null ? ',' : ccfg::get('thousand_separator');
        $variables['thousandSeparator'] = ccfg::get('thousand_separator') === null ? ',' : ccfg::get('thousand_separator');
        $variables['decimal_digit'] = ccfg::get('decimal_digit') === null ? '0' : ccfg::get('decimal_digit');
        $variables['decimalDigit'] = ccfg::get('decimal_digit') === null ? '0' : ccfg::get('decimal_digit');
        $variables['have_clock'] = ccfg::get('have_clock') === null ? false : ccfg::get('have_clock');
        $variables['haveClock'] = ccfg::get('have_clock') === null ? false : ccfg::get('have_clock');
        $variables['have_scroll_to_top'] = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');
        $variables['haveScrollToTop'] = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');


        $bootstrap = ccfg::get('bootstrap');
        $themeData = CManager::instance()->get_theme_data();
        if (isset($themeData) && strlen(carr::get($themeData, 'bootstrap')) > 0) {
            $bootstrap = carr::get($themeData, 'bootstrap');
        }

        if (strlen($bootstrap) == 0) {
            $bootstrap = '2.3';
        }
        $variables['bootstrap'] = $bootstrap;

        $variables['base_url'] = curl::base();
        $variables['baseUrl'] = curl::base();
        $variables['label_confirm'] = clang::__("Are you sure ?");
        $variables['labelConfirm'] = clang::__("Are you sure ?");
        $variables['label_ok'] = clang::__("OK");
        $variables['labelOk'] = clang::__("OK");
        $variables['label_cancel'] = clang::__("Cancel");
        $variables['labelCancel'] = clang::__("Cancel");
        return $variables;
    }

    public function setViewName($viewName) {
        $this->viewName = $viewName;
    }

    public function setViewLoginName($viewLoginName) {
        $this->viewLoginName = $viewLoginName;
    }

    /**
     * 
     * @return void
     */
    public function __destruct() {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

}
