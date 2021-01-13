<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @mixin CElement
 */
class CApp implements CInterface_Responsable, CInterface_Renderable, CInterface_Jsonable {
    use CTrait_Compat_App,
        CTrait_Macroable,
        CTrait_RequestInfoTrait,
        CApp_Concern_Navigation,
        CApp_Concern_ManageStackTrait,
        CApp_Trait_App_Breadcrumb,
        CApp_Trait_App_Variables,
        CApp_Trait_App_View,
        CApp_Trait_App_Renderer,
        CApp_Trait_App_Auth,
        CApp_Trait_App_Bootstrap,
        CApp_Trait_App_Title;

    private $content = '';
    private $js = '';
    private $custom_js = '';
    private $custom_header = '';
    private $custom_footer = '';
    private $custom_data = [];
    private $signup = false;
    private $activation = false;
    private $resend = false;
    private $org = null;
    public static $instance = null;
    private $header_body = '';
    private $additional_head = '';
    private $ajaxData = [];
    private $renderMessage = true;
    private $keepMessage = false;
    private $useRequireJs = false;
    private static $haveScrollToTop = null;
    protected $renderer;

    private static $renderingElement;

    /**
     * @var CApp_Element
     */
    protected $element;

    /**
     * @param string $domain
     *
     * @return CApp_Navigation
     */
    public static function navigation($domain = null) {
        return CApp_Navigation::instance($domain);
    }

    /**
     * @param string $domain
     *
     * @return CApp_Api
     */
    public static function api($domain = null) {
        return CApp_Api::instance($domain);
    }

    /**
     * @return CApp_SEO
     */
    public static function seo() {
        return CApp_SEO::instance();
    }

    /**
     * @param string $domain
     * @param mixed  $options
     *
     * @return CApp_Remote
     */
    public static function remote($domain = null, $options = []) {
        return CApp_Remote::instance($domain, $options);
    }

    /**
     * @param string $modelName
     *
     * @return CApp_Model
     */
    public static function model($modelName) {
        $modelClass = 'CApp_Model_' . $modelName;
        return new $modelClass();
    }

    /**
     * @param string $domain
     *
     * @return CApp_Navigation
     */
    public static function nav($domain = null) {
        return CApp_Navigation::instance($domain);
    }

    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    public static function auth() {
        return CApp_Auth::instance();
    }

    /**
     * @return CApp_Temp
     */
    public static function temp() {
        return new CApp_Temp();
    }

    /**
     * @return CApp_Data
     */
    public static function data() {
        return new CApp_Data();
    }

    public static function getTranslation($message, $params = [], $lang = null) {
        return CF::lang($message, $params, $lang);
    }

    public static function component() {
        return CComponent_Manager::instance();
    }

    /**
     * @param null|mixed $domain
     * @param null|mixed $dbName
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

    /**
     * Get Translator instance
     *
     * @return CTranslation_Translator
     */
    public function translator() {
        return CTranslation::translator();
    }

    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        $validation = CValidation::factory();
        return $validation->validate($data, $rules, $messages, $customAttributes);
    }


    public function __construct($domain = null) {
        $this->element = new CApp_Element();

        $this->org = corg::get(CF::orgCode());

        //we load another configuration for this app
        //org configuration
        if (strlen(CF::orgCode()) > 0) {
            $orgBootFile = DOCROOT . 'application' . DS . $this->code() . DS . CF::orgCode() . DS . CF::orgCode() . EXT;
            if (file_exists($orgBootFile)) {
                include $orgBootFile;
            }
        }

        $appBootFile = DOCROOT . 'application' . DS . $this->code() . DS . $this->code() . EXT;

        if (file_exists($appBootFile)) {
            include $appBootFile;
        }

        $org = $this->org();

        if (ccfg::get('set_timezone')) {
            $timezone = ccfg::get('default_timezone');
            if ($org != null) {
                //$timezone = $org->timezone;
            }

            date_default_timezone_set($timezone);
        }

        $this->id = 'capp';
        //check login

        if (ccfg::get('update_last_request')) {
            $user = $this->user();
            if ($user != null) {
                if (!is_array($user) && is_object($user)) {
                    //update last request
                    $db = $this->db();
                    $db->update('users', ['last_request' => date('Y-m-d H:i:s')], ['user_id' => $user->user_id]);
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

    /**
     * @deprecated
     *
     * @return bool
     */
    public static function isAdmin() {
        return static::isAdministrator();
    }

    /**
     * @param bool $install
     *
     * @return CApp
     */
    public static function factory($install = false) {
        //return new CApp($install);
        return self::instance($install);
    }

    /**
     * @param null|string $domain
     *
     * @return CApp
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (self::$instance == null) {
            self::$instance = [];
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

    public function reset() {
        $this->rendered = false;
        $this->element->clear();
        return $this;
    }

    public function addCustomData($key, $value) {
        if (!is_array($this->custom_data)) {
            $this->custom_data = [];
        }
        $this->custom_data[$key] = $value;
        return $this;
    }

    public function setCustomData($data) {
        $this->custom_data = $data;
        return $this;
    }

    public function getCustomData($key = null, $default = null) {
        if ($key === null) {
            return $this->custom_data;
        }
        return carr::get($this->custom_data, $key, $default);
    }

    public function org() {
        if ($this->org == null) {
            $role = $this->role();
            if ($role != null) {
                $this->org = corg::get($role->org_id);
            }
        }
        return $this->org;
    }

    public function orgId() {
        $org = $this->org();
        if ($org == null) {
            return null;
        }
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
        $childList = [];

        $traverse = function ($childs) use (&$traverse, &$childList) {
            foreach ($childs as $child) {
                $depth = carr::get($child, 'depth');
                $childList[$child['role_id']] = cutils::indent($depth, '&nbsp;&nbsp;') . $child['name'];
                $traverse($child->getChildren);
            }
        };

        $traverse($nodes);

        return $childList;
    }

    public function toArray() {
        $data = [];
        $data['title'] = $this->title;
        $message = '';
        $messageOrig = '';
        if (!$this->keepMessage) {
            $messageOrig = cmsg::flash_all();
            if ($this->renderMessage) {
                $message = $messageOrig;
            }
        }
        $data['html'] = $message . $this->html();
        $asset = CManager::asset();
        $js = $this->js();

        $js = $asset->renderJsRequire($js, 'cresenity.cf.require');
        $data['js'] = base64_encode($js);
        $data['css_require'] = $asset->getAllCssFileUrl();
        $data['message'] = $messageOrig;
        $data['ajaxData'] = $this->ajaxData;
        $data['html'] = mb_convert_encoding($data['html'], 'UTF-8', 'UTF-8');
        return $data;
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        $data = $this->toArray();
        return json_encode($data, $options);
    }

    /**
     * Alias of toJson
     *
     * @param int $options
     *
     * @return string
     */
    public function json($options = 0) {
        return $this->toJson($options);
    }

    /**
     * @return void
     */
    public function __destruct() {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    public static function sendExceptionEmail(Exception $exception, $email = null) {
        if (!($exception instanceof CHTTP_Exception_NotFoundHttpException)) {
            $html = CApp_ErrorHandler::sendExceptionEmail($exception, $email);
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
     * @param CHTTP_Request $request
     *
     * @return CHTTP_Response
     */
    public function toResponse($request) {
        if (c::request()->ajax()) {
            return c::response()->json($this);
        }
        return CHTTP::createResponse($this->render());
    }

    public static function isAdministrator() {
        return carr::first(explode('/', trim(CFRouter::getUri(), '/'))) == 'administrator';
    }

    public static function setTheme($theme) {
        CManager::theme()->setTheme($theme);
    }

    public static function renderingElement() {
        return static::$renderingElement;
    }

    public static function setRenderingElement($element) {
        static::$renderingElement = $element;
    }

    public static function setHaveScrollToTop($bool = true) {
        static::$haveScrollToTop = $bool;
    }

    public static function haveScrollToTop() {
        if (static::$haveScrollToTop === null) {
            static::$haveScrollToTop = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');
        }
        return static::$haveScrollToTop;
    }
}
