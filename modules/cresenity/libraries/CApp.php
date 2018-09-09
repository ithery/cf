<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CApp extends CObservable {

    use CTrait_Compat_App;

    private $title = "";
    private $content = "";
    private $js = "";
    private $custom_js = "";
    private $custom_header = "";
    private $custom_footer = "";
    private $custom_data = array();
    private $show_breadcrumb = true;
    private $show_title = true;
    private $breadcrumb = array();
    private $signup = false;
    private $activation = false;
    private $resend = false;
    private $login_required = true;
    private $_store = null;
    private $_role = null;
    private $_org = null;
    private $_user = null;
    private $_admin = null;
    private $_member = null;
    public static $_instance = null;
    private $run;
    protected $rendered = false;
    private $header_body = '';
    private $additional_head = '';
    private $ajaxData = array();
    private $renderMessage = true;
    private $keepMessage = false;
    private $viewName = 'cpage';
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

    public function setup($install = false) {

        if ($this->run) {
            return;
        }

        if (isset($_COOKIE['capp-profiler'])) {
            new Profiler();
        }
        if (isset($_COOKIE['capp-debugbar'])) {
            CDebug::bar()->enable();
        }
        $db = CDatabase::instance();
        if ($this->_org == null) {
            $org_id = cstg::get("org_id");
            if (strlen($org_id) > 0) {
                $this->_org = cstg::get($org_id);
            }
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
        $post = $_POST;
        if (!$install) {
            $config['default'] = array(); // prevents merging the database config with system/config/database.php

            if (!file_exists(DOCROOT . 'modules/cresenity/config/database.php')) {
                header('Location: ' . curl::base() . 'cresenity/install');
                exit;
            }
        }

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


        if (ccfg::get('mail_error')) {

            // Set error handler
            set_error_handler(array('CApp', 'exception_handler'));

            // Set exception handler
            set_exception_handler(array('CApp', 'exception_handler'));
        }

        $this->run = true;
    }

    public function __construct($install = false) {

        parent::__construct();


        $this->_org = corg::get(CF::orgCode());

        $this->run = false;

        $theme_path = "";
    }

    public function setLoginRequired($bool) {
        return $this->login_required = $bool;
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

    public function controller() {
        return CF::instance();
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
    public static function instance($install = false) {
        if (self::$_instance == null) {
            self::$_instance = new CApp($install);
            self::$_instance->setup($install);
        }
        return self::$_instance;
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

    public function title($title) {
        $this->title = clang::__($title);
        return $this;
    }

    public function showBreadcrumb($bool) {
        $this->show_breadcrumb = $bool;
        return $this;
    }

    public function showTitle($bool) {
        $this->show_title = $bool;
        return $this;
    }

    public function addCustomJs($js) {
        $this->custom_js .= $js;
        return $this;
    }

    public function set_view_html() {
        
    }

    /**
     * 
     * @param string $caption
     * @param string $url
     * @return CApp
     */
    public function addBreadcrumb($caption, $url) {
        $this->breadcrumb[$caption] = $url;
        return $this;
    }

    public function registerCoreModules() {
        $manager = CManager::instance();
        $theme = CManager::theme()->getCurrentTheme();
        $themeFile = CF::get_file('themes', $theme);
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
                    $cs->registerCssFiles($css);
                }
            }
            if ($jsArray != null) {
                foreach ($jsArray as $js) {
                    $cs->registerJsFiles($js);
                }
            }
        }
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
        $theme_path = '';

        $theme = ctheme::get_current_theme();

        $themeFile = CF::get_file('themes', $theme);
        if (file_exists($themeFile)) {
            $themeData = include $themeFile;
            $theme_path = carr::get($themeData, 'theme_path');
            if ($theme_path == null) {
                $theme_path = '';
            } else {
                $theme_path .= '/';
            }
        }
        $viewName = $this->viewName;
        if (ccfg::get("install")) {
            $viewName = 'cinstall/page';
        } else if ($this->signup) {
            $viewName = 'ccore/signup';
        } else if ($this->resend) {
            $viewName = 'ccore/resend_activation';
        } else if ($this->activation) {
            $viewName = 'ccore/activation';
        } else if (!$this->is_user_login() && ccfg::get("have_user_login") && $this->login_required) {
            $viewName = 'ccore/login';
        } else if (!$this->is_user_login() && ccfg::get("have_static_login") && $this->login_required) {
            $viewName = 'ccore/static_login';
        }

        if (self::$viewCallback != null && is_callable(self::$viewCallback)) {
            $viewName = self::$viewCallback($viewName);
        }

        if (CView::exists($theme_path . $viewName)) {
            $v = CView::factory($theme_path . $viewName);
        }
        if ($v == null) {
            if (!CView::exists($viewName)) {
                throw new CApp_Exception('view :viewName not exists', array(':viewName' => $viewName));
            }
            $v = CView::factory($viewName);
        }
        $this->content = parent::html();
        $this->js = parent::js();

        $v->content = $this->content;
        $v->header_body = $this->header_body;

        $v->title = $this->title;
        $asset = CManager::asset();

        $css_urls = $asset->getAllCssFileUrl();

        $js_urls = $asset->getAllCssFileUrl();
        $additional_js = "";

        foreach ($css_urls as $url) {

            $additional_js .= "
					$.cresenity._filesadded+='['+'" . $url . "'+']'
				";
        }
        $js = "";
        //$vjs = CView::factory('ccore/js');
        //$js .= PHP_EOL . $vjs->render();

        $js .= PHP_EOL . $this->js . $additional_js;

        $js = $asset->renderJsRequire($js);

        if (ccfg::get("minify_js")) {
            $js = CJSMin::minify($js);
        }

        $v->js = $js;

        $v->css_hash = "";
        $v->js_hash = "";
        if (ccfg::get("merge_css")) {
            $v->css_hash = $cs->create_css_hash();
        }
        if (ccfg::get("merge_js")) {
            $v->js_hash = $cs->create_js_hash();
        }

        $v->theme = $theme;
        $v->theme_path = $theme_path;
        $v->head_client_script = "";
        $v->begin_client_script = "";
        $v->end_client_script = "";

        $v->load_client_script = "";
        $v->ready_client_script = "";


        $v->head_client_script = $asset->render('head');
        $v->begin_client_script = $asset->render('begin');
        //$v->end_client_script = $asset->render('end');

        $v->load_client_script = $asset->render('load');
        $v->ready_client_script = $asset->render('ready');

        $v->custom_js = $this->custom_js;
        $v->custom_header = $this->custom_header;
        $v->custom_footer = $this->custom_footer;
        $v->show_breadcrumb = $this->show_breadcrumb;
        $v->show_title = $this->show_title;
        $v->breadcrumb = $this->breadcrumb;
        $v->additional_head = $this->additional_head;
        $v->custom_data = $this->custom_data;
        $v->login_required = $this->login_required;



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
        return $this->_admin;
    }

    public function user() {
        if ($this->_user == null) {
            $session = CSession::instance();
            $user = $session->get("user");
            if (!$user)
                $user = null;
            $this->_user = $user;
        }
        return $this->_user;
    }

    public function role() {
        if ($this->_role == null) {
            $user = $this->user();
            if ($user != null) {
                $this->_role = crole::get(cobj::get($user, 'role_id'));
            }
        }
        return $this->_role;
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

    public function is_user_login() {
        return $this->user() != null;
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

        $traverse = function ($childs, $level = 0) use (&$traverse, &$childList) {
            foreach ($childs as $child) {
                $childList[$child["role_id"]] = cutils::indent($level, "&nbsp;&nbsp;&nbsp;&nbsp;") . $child["name"];
                $traverse($child->getChildren, ++$level);
            }
        };

        $traverse($nodes);

        return $childList;
    }

    public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL) {

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
            if ($line != FALSE) {
                // Remove the first entry of debug_backtrace(), it is the exception_handler call
                $trace = $PHP_ERROR ? array_slice(debug_backtrace(), 1) : $exception->getTrace();

                // Beautify backtrace
                $trace = CF::backtrace($trace);
            }



            $v = CView::factory('cmail/error_mail');
            $v->error = $error;
            $v->description = $description;
            $v->file = $file;
            $v->line = $line;
            $v->trace = $trace;
            $v->message = $message;
            $html = $v->render();

            cmail::error_mail($html);


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
        $variables['thousand_separator'] = ccfg::get('thousand_separator') === null ? ',' : ccfg::get('thousand_separator');
        $variables['decimal_digit'] = ccfg::get('decimal_digit') === null ? '0' : ccfg::get('decimal_digit');
        $variables['have_clock'] = ccfg::get('have_clock') === null ? false : ccfg::get('have_clock');
        $variables['have_scroll_to_top'] = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');

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
        $variables['label_confirm'] = clang::__("Are you sure ?");
        $variables['label_ok'] = clang::__("OK");
        $variables['label_cancel'] = clang::__("Cancel");
        return $variables;
    }

    public function setViewName($viewName) {
        $this->viewName = $viewName;
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
