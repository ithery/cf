<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @mixin CElement
 */
class CApp implements CInterface_Responsable {

    use CTrait_Compat_App,
        CTrait_Macroable,
        CTrait_RequestInfoTrait,
        CApp_Trait_App_Breadcrumb,
        CApp_Trait_App_Variables,
        CApp_Trait_App_View,
        CApp_Trait_App_Renderer,
        CApp_Trait_App_Auth,
        CApp_Trait_App_Bootstrap,
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
    private $_org = null;
    public static $instance = null;
    private $header_body = '';
    private $additional_head = '';
    private $ajaxData = array();
    private $renderMessage = true;
    private $keepMessage = false;
    private $useRequireJs = false;
    protected $renderer;

    /**
     *
     * @var CApp_Element
     */
    protected $element;

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
     * @return CApp_SEO
     */
    public static function seo() {
        return CApp_SEO::instance();
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

    public static function component() {
        return CComponent_Manager::instance();
    }

    /**
     * 
     * @return CDatabase
     */
    public static function db($domain = null, $dbName = null) {
        return CDatabase::instance($dbName, null, $domain);
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

    public function isUseRequireJs() {
        return $this->useRequireJs ? true : false;
    }

    public function __construct($domain = null) {


        $this->element = new CApp_Element();

        $this->_org = corg::get(CF::orgCode());
        $this->useRequireJs = ccfg::get('requireJs');


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
        return strlen(CF::appCode()) > 0 ? CF::appCode() : CF::appCode();
    }

    public function code() {
        return CF::appCode();
    }

    public function message($type, $message) {
        return CApp_Message::add($type, $message);
    }

    public function controller() {
        return CHTTP::kernel()->controller();
    }

    public static function config($path, $domain = null) {
        return CApp_Config::get($path, $domain);
    }

    public function set_header_body($header_body) {
        $this->header_body = $header_body;
        return $this;
    }

    /**
     * 
     * @deprecated
     * @return bool
     */
    public static function isAdmin() {
        return static::isAdministrator();
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
        if (self::$instance == null) {
            self::$instance = array();
        }
        if (!isset(self::$instance[$domain])) {
            self::$instance[$domain] = new CApp($domain);
        }
        return self::$instance[$domain];
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

    public function reset() {
        $this->rendered = false;
        $this->element->clear();
        return $this;
    }

    public function addCustomData($key, $value) {
        if (!is_array($this->custom_data)) {
            $this->custom_data = array();
        }
        $this->custom_data[$key] = $value;
        return $this;
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

    public function getRoleChildList($roleId = null, $orgId = null, $type = null) {
        if (strlen($roleId) == 0) {
            $roleId = $this->role()->role_id;
        }
        if (strlen($orgId) == 0) {
            $orgId = CApp_Base::orgId();
        }

        $nodes = self::model('Roles')->getDescendantsTree($roleId, $orgId, $type);
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


        if ($exception instanceof \Pheanstalk\Exception\ServerException) {
            return;
        }
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

        if ($asset->isUseRequireJs()) {
            $js = $asset->renderJsRequire($js);
        } else {
            $js = $asset->renderJsRequire($js, 'cresenity.cf.require');
        }
        if (ccfg::get("minify_js")) {
            $js = CJSMin::minify($js);
        }
        $data["js"] = cbase64::encode($js);
        $data["css_require"] = $asset->getAllCssFileUrl();
        $data["message"] = $messageOrig;
        $data["ajaxData"] = $this->ajaxData;
        $data['html'] = mb_convert_encoding($data['html'], 'UTF-8', 'UTF-8');
        return cjson::encode($data);
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

    public static function sendExceptionEmail(Exception $exception, $email = null) {


        if (!($exception instanceof CF_404_Exception)) {

            $html = CApp_ErrorHandler::sendExceptionEmail($exception, $email = null);
        }
    }

    public function __call($method, $parameters) {
        if (method_exists($this->element, $method)) {
            return call_user_func_array([$this->element, $method], $parameters);
        }
        if ($this->element->hasMacro($method)) {
            return call_user_func_array([$this->element, $method], $parameters);
        }


        throw new Exception('undefined method on CApp: ' . $method);
    }

    /**
     * 
     * @param CHTTP_Request $request
     * @return CHTTP_Response
     */
    public function toResponse($request) {
        return CHTTP::createResponse($this->render());
    }

    public static function isAdministrator() {
        return carr::first(explode("/", trim(CFRouter::getUri(), "/"))) == "administrator";
    }

    public function setTheme($theme) {
        CManager::theme()->setTheme($theme);
    }

}
