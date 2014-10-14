<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CApp extends CObservable {

    private $title = "";
    private $content = "";
    private $js = "";
    private $custom_js = "";
    private $custom_header = "";
    private $custom_footer = "";
    private $show_breadcrumb = true;
    private $show_title = true;
    private $breadcrumb = array();
    private $signup = false;
    private $activation = false;
    private $resend = false;
    private $_store = null;
    private $_role = null;
    private $_org = null;
    private $_user = null;
    private $_admin = null;
    private $_member = null;
    private $_app_id = null;
    private $_clientmodules;
    public static $_instance = null;
    private $app_list = null;
    private $run;

    public function __destruct() {
        if (function_exists('gc_collect_cycles')) {

            gc_collect_cycles();
        }
    }

    public function setup($install = false) {
		
        if ($this->run)
            return;

        $this->register_core_modules();


        $db = CDatabase::instance();
        if ($this->_org == null) {
            $org_id = cstg::get("org_id");
            if (strlen($org_id) > 0) {
                $this->_org = cstg::get($org_id);
            }
        }

        //check for admin or app
        $router_uri = CFRouter::routed_uri(CFRouter::$current_uri);
        $rsegment = explode('/', $router_uri);
        if (count($rsegment) > 0) {
            if ($rsegment[0] == "admin") {
                $this->_app_id = 0;
            }
        }

        //we load another configuration for this app
        $app_boot_file = DOCROOT . "application" . DS . $this->code() . DS . $this->code() . EXT;
        if (file_exists($app_boot_file)) {
            include($app_boot_file);
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

        /*
          $domain = crouter::domain();
          if (cstg::get("domain"))
          $domain = cstg::get("domain");
          //get domain data
          $domain_data = cdata::get($domain, 'domain');
          $this->_app_id = 1;
          if ($domain_data != null) {
          $this->_org = corg::get($domain_data['org_id']);
          $this->_app_id = $domain_data['app_id'];
          }
         */

        $this->_app_id = CF::app_id();

        $this->_org = corg::get(CF::org_code());

        $this->run = false;
    }

    public function app_id() {
        return $this->_app_id;
    }

    public function name() {
        //$app = CJDB::instance()->get("app", array("app_id" => $this->app_id()));
        //return $app[0]->name;
    }

    public function code() {
        //$app = CJDB::instance()->get("app", array("app_id" => $this->app_id()));
        //return $app[0]->code;
        return CF::app_code();
    }

    public function controller() {
        return CF::instance();
    }

    public function app_list() {
        if ($this->app_list == null) {
            //we will get all available app for this org
            $cdb = CJDB::instance();
            $data = $cdb->get('domain', array("org_id" => $this->org()->org_id))->result_array();
            $this->app_list = array();
            foreach ($data as $domain) {
                $app_id = $domain["app_id"];
                $app = $cdb->get('app', array('app_id' => $app_id));
                $app_name = $app[0]->name;
                $this->app_list[$app_id] = $app_name;
            }
            //if array is empty, we make default is app with id 1
            if (empty($this->app_list)) {
                $app_id = 1;
                $app = $cdb->get('app', array('app_id' => $app_id));
                $app_name = $app[0]->name;
                $this->app_list[$app_id] = $app_name;
            }
        }

        return $this->app_list;
    }

    public function store_list() {
        $cdb = CJDB::instance();
        $stores = $cdb->get("store", array("org_id" => $this->org()->org_id));
        $result = array();
        foreach ($stores as $store) {
            $result[$store->store_id] = $store->name;
        }
        return $result;
    }

    public function have_store() {

        $cdb = CJDB::instance();
        $stores = $cdb->get("store", array("org_id" => $this->org()->org_id));
        return $stores->count() > 0;
    }

    public function is_store() {
        /*
          if (ccfg::get("store_id"))
          return true;
          return false;
         */
        return strlen(CF::$store_code) > 0;
    }

    public function have_store_access($store_id) {
        $db = CDatabase::instance();
        $org_id = $this->org()->org_id;
        $user_id = $this->user()->user_id;
        $q = "select count(*) as cnt from users_store where store_id=" . $db->escape($store_id) . " and org_id=" . $db->escape($org_id) . " and user_id=" . $db->escape($user_id);
        $val = cdbutils::get_value($q);
        return $val > 0;
    }

    public function user_store_list() {
        $cdb = CJDB::instance();
        $org_id = $this->org()->org_id;
        $all_store_list = $cdb->get_list('store', 'store_id', 'name', array("org_id" => $org_id));
        $store_list = array();
        foreach ($all_store_list as $k => $v) {
            if ($this->have_store_access($k)) {
                $store_list[$k] = $v;
            }
        }
        return $store_list;
    }

    public function menu_list() {
        $cdb = CJDB::instance();
        $org_id = $this->org()->org_id;
        $all_menu_list = $cdb->get_list('resto_menu', 'resto_menu_id', 'name', array("org_id" => $org_id));
        $menu_list = array();
        foreach ($all_menu_list as $k => $v) {
            if ($this->have_store_access($k)) {
                $menu_list[$k] = $v;
            }
        }
        return $menu_list;
    }

    public function db() {
        return CDatabase::instance();
    }

    public function is_admin() {
        return $this->app_id() == 0;
    }

    public static function factory($install = false) {
        //return new CApp($install);
        return self::instance($install);
    }

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
        $this->title = $title;
        return $this;
    }

    public function show_breadcrumb($bool) {
        $this->show_breadcrumb = $bool;
        return $this;
    }

    public function show_title($bool) {
        $this->show_title = $bool;
        return $this;
    }

    public function add_custom_js($js) {
        $this->custom_js.= $js;
        return $this;
    }

    public function set_view_html() {
        
    }

    public function add_breadcrumb($caption, $url) {
        $this->breadcrumb[$caption] = $url;
        return $this;
    }

    public function register_client_module($module) {
        CManager::instance()->register_module($module);
    }

    public function register_core_modules() {
		if(ccfg::get('require_js')) {
			
			$this->register_client_module('json2');
			$this->register_client_module('excanvas');
			$this->register_client_module('canvas-to-blob');
			$this->register_client_module('load-image');

			$this->register_client_module('jquery');
			$this->register_client_module('bootstrap');
			$this->register_client_module('font-awesome');
			$this->register_client_module('jquery.ui');
			$this->register_client_module('jquery.dialog2');
			$this->register_client_module('cresenity');
			$this->register_client_module('fileupload');
			$this->register_client_module('wysihtml5');
			$this->register_client_module('notify');
			$this->register_client_module('bootbox');
			$this->register_client_module('form');
			$this->register_client_module('controls');
			$this->register_client_module('event');
			$this->register_client_module('slimscroll');
			$this->register_client_module('effects');
			$this->register_client_module('validation');
			
			$this->register_client_module('easing');
			
			$this->register_client_module('chosen');
			$this->register_client_module('uniform');
			$this->register_client_module('select2');
			$this->register_client_module('image-gallery');
			$this->register_client_module('modernizr');
			$this->register_client_module('multiselect');
			$this->register_client_module('elfinder');
			$this->register_client_module('prettify');
			$this->register_client_module('bootstrap-switch');
		} else {
			$this->register_client_module('json2');
			$this->register_client_module('excanvas');
			$this->register_client_module('canvas-to-blob');
			$this->register_client_module('load-image');

			$this->register_client_module('jquery');
			$this->register_client_module('bootstrap');
			$this->register_client_module('font-awesome');
			$this->register_client_module('jquery.ui');
			$this->register_client_module('jquery.dialog2');
			$this->register_client_module('cresenity');
			$this->register_client_module('fileupload');
			$this->register_client_module('wysihtml5');
			$this->register_client_module('notify');
			$this->register_client_module('bootbox');
			$this->register_client_module('form');
			$this->register_client_module('controls');
			$this->register_client_module('event');
			$this->register_client_module('slimscroll');
			$this->register_client_module('effects');
			$this->register_client_module('validation');
			
			$this->register_client_module('easing');
			
			$this->register_client_module('chosen');
			$this->register_client_module('uniform');
			$this->register_client_module('select2');
			$this->register_client_module('image-gallery');
			$this->register_client_module('modernizr');
			$this->register_client_module('multiselect');
			$this->register_client_module('elfinder');
			$this->register_client_module('prettify');
			$this->register_client_module('bootstrap-switch');
		}
		if(ccfg::get('have_clock')) {
			$this->register_client_module('servertime');
        }
    }

    public function render() {
		
		if(crequest::is_ajax()) {
			return $this->json();
			
		}
		
        $theme_path = "";
        $theme_path = ctheme::path();

        if (ccfg::get("install")) {
            $v = CView::factory($theme_path . 'cinstall/page');
            /*
              } else if ($this->is_admin()) {
              if (!$this->is_admin_login()) {
              $v = CView::factory('admin/login');
              } else {
              $v = CView::factory('admin/cpage');
              $this->content = parent::html();
              $this->js = parent::js();
              $v->content = $this->content;
              $v->title = $this->title;
              $v->js = $this->js;
              $cs = CClientScript::instance();
              $v->head_client_script = $cs->render('head');
              $v->begin_client_script = $cs->render('begin');
              $v->end_client_script = $cs->render('end');
              $v->load_client_script = $cs->render('load');
              $v->ready_client_script = $cs->render('ready');

              $v->custom_js = $this->custom_js;
              $v->custom_header = $this->custom_header;
              $v->custom_footer = $this->custom_footer;
              $v->show_breadcrumb = $this->show_breadcrumb;
              $v->show_title = $this->show_title;
              $v->breadcrumb = $this->breadcrumb;
              }
             */
        } else if ($this->signup) {
            $v = CView::factory($theme_path . 'ccore/signup');
        } else if ($this->resend) {
            $v = CView::factory($theme_path . 'ccore/resend_activation');
        } else if ($this->activation) {
            $v = CView::factory($theme_path . 'ccore/activation');
        } else if (!$this->is_user_login() && ccfg::get("have_user_login")) {
            $v = CView::factory($theme_path . 'ccore/login');
        } else if (!$this->is_user_login() && ccfg::get("have_static_login")) {
            $v = CView::factory($theme_path . 'ccore/static_login');
        } else {
            $v = CView::factory($theme_path . 'cpage');

            $this->content = parent::html();
            $this->js = parent::js();
            $v->content = $this->content;
            $v->title = $this->title;
            $cs = CClientScript::instance();
			$css_urls = $cs->url_css_file();
			
			$js_urls = $cs->url_js_file();
			$additional_js = "";
			
			foreach($css_urls as $url) {
				
				$additional_js .= "
					$.cresenity._filesadded+='['+'".$url."'+']'
				";
			}
			
			$js = $this->js.$additional_js;
			$vjs = CView::factory('ccore/js');
			$js.=PHP_EOL.$vjs->render();
			
			$js = $cs->render_js_require($js);
			
			if(ccfg::get("minify_js")) {
				$js = CJSMin::minify($js);
			}
			
			$v->js = $js;
            
			$v->css_hash = "";
			$v->js_hash = "";
			if(ccfg::get("merge_css")) {
				$v->css_hash = $cs->create_css_hash();
			}
			if(ccfg::get("merge_js")) {
				$v->js_hash = $cs->create_js_hash();
			}

			$v->head_client_script = "";
            $v->begin_client_script = "";
            $v->end_client_script = "";

            $v->load_client_script = "";
            $v->ready_client_script = "";
			
			
			$v->head_client_script = $cs->render('head');
            $v->begin_client_script = $cs->render('begin');
            // $v->end_client_script = $cs->render('end');

            $v->load_client_script = $cs->render('load');
            $v->ready_client_script = $cs->render('ready');
			
            $v->custom_js = $this->custom_js;
            $v->custom_header = $this->custom_header;
            $v->custom_footer = $this->custom_footer;
            $v->show_breadcrumb = $this->show_breadcrumb;
            $v->show_title = $this->show_title;
            $v->breadcrumb = $this->breadcrumb;
        }

        return $v->render();
    }

    public function admin() {
        if ($this->_admin == null) {
            $session = Session::instance();
            $admin = $session->get("admin");
            if (!$admin)
                $admin = null;
            $this->_admin = $admin;
        }
        return $this->_admin;
    }

    public function member() {
        if ($this->_member = null) {
            $session = Session::instance();
            $member = $session->get("member");
            if (!$member)
                $member = null;
            $this->_member = $member;
        }
        return $this->_admin;
    }

    public function user() {
        if ($this->_user == null) {
            $session = Session::instance();
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
                if (is_object($user)) {
                    if (property_exists($user, 'role_id')) {
                        $this->_role = crole::get($user->role_id);
                    }
                }
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
                $this->_store = cstore::get(CF::org_code(), CF::store_code());
            }
        }
        return $this->_store;
    }

    public function get_child_array($id = "", $level = 0) {
        $db = CDatabase::instance();
        $q = "select role_id,name from roles where status>0 ";
        if (strlen($id) > 0) {
            $q.=" and parent_id=" . $db->escape($id);
        }
        $result = array();
        $r = $db->query($q);
        foreach ($r as $row) {
            $role = array();
            $role["id"] = $row->role_id;
            $role["name"] = $row->name;
            $role["level"] = $level;
            $result[] = $role;
            $childs = $this->get_child_array($row->role_id, $level + 1);
            if (count($childs) > 0)
                $result = array_merge($result, $childs);
        }
        return $result;
    }

    public function get_role_child_list($role_id = null) {
        if (strlen($role_id) == 0)
            $role_id = $this->role()->role_id;
        $child_array = $this->get_child_array($role_id);
        $child_list = array();


        foreach ($child_array as $child) {

            $child_list[$child["id"]] = cutils::indent($child["level"], "&nbsp;&nbsp;&nbsp;&nbsp;") . $child["name"];
        }
        return $child_list;
    }

    public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL) {

        try {
            $app = CApp::instance();
            $org = $app->org();

            // PHP errors have 5 args, always
            $PHP_ERROR = (func_num_args() === 5);

            // Test to see if errors should be displayed
            if ($PHP_ERROR AND (error_reporting() & $exception) === 0)
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
        $data["html"] = cmsg::flash_all() . $this->html();
		$js = $this->js();
		$js = CClientScript::instance()->render_js_require($js);
		if(ccfg::get("minify_js")) {
			$js = CJSMin::minify($js);
		}
        $data["js"] = cbase64::encode($js);
		$data["css_require"] = CClientScript::instance()->url_css_file();
        return cjson::encode($data);
    }

}