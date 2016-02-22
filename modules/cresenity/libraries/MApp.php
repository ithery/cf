<?php


    defined('SYSPATH') OR die('No direct access allowed.');

    class MApp extends CMobile_Observable {

        private $run;
		public static $_instance = null;
		private $rendered = false;
		private $login_required = true;
		private $_role = null;
        private $_org = null;
        private $_user = null;
        private $_app_id = null;
		private $_mobile_path;
		
		public function title($title) {
			$this->page()->set_title($title);
		}
		
		public function page() {
			return CPage::instance();
		}
		
		public function set_mobile_path($path) {
			CManager::instance()->set_mobile_path($path);
			return $this;
		}
		
		
		
        public function __destruct() {
            if (function_exists('gc_collect_cycles')) {

                gc_collect_cycles();
            }
        }

        
        public function setup($install = false) {

            if ($this->run) return;

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
            //org configuration
            if (strlen(CF::org_code()) > 0) {
                $org_boot_file = DOCROOT . "application" . DS . $this->code() . DS . CF::org_code() . DS . CF::org_code() . EXT;
                if (file_exists($org_boot_file)) {
                    include($org_boot_file);
                }
            }


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

            $this->id = "mapp";
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
                set_error_handler(array('MApp', 'exception_handler'));

                // Set exception handler
                set_exception_handler(array('MApp', 'exception_handler'));
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

            $theme_path = "";
            
        }

      
		public function set_login_required($bool) {
            return $this->login_required=$bool;
        }
		
		
        public function app_id() {
            return $this->_app_id;
        }

        public function manager() {
            return CManager::instance();
        }

        public function code() {
            return CF::app_code();
        }

        public function controller() {
            return CF::instance();
        }

        public function db() {
            return CDatabase::instance();
        }

        public static function instance($install = false) {
            if (self::$_instance == null) {
                self::$_instance = new MApp($install);
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
            
			
            
			$theme = ccfg::get('theme');
			if($theme==null) $theme = 'cresenity';
			$theme_file = CF::get_file('themes',$theme);
			if(file_exists($theme_file)) {
				$theme_data = include $theme_file;
				$module_arr = carr::get($theme_data,'client_modules');
				$css_arr = carr::get($theme_data,'css');
				$js_arr = carr::get($theme_data,'js');
				$cs = CClientScript::instance();
				if($module_arr!=null) {
					foreach($module_arr as $module) {
						$this->register_client_module($module);
					}
				}
				if (ccfg::get('have_clock')) {
					$this->register_client_module('servertime');
				}
				if($css_arr!=null) {
					foreach($css_arr as $css) {
						$cs->register_css_files($css);
					}
				}
				if($js_arr!=null) {
					foreach($js_arr as $js) {
						$cs->register_js_files($js);
					}
				}
			}
        }

        

        public function rendered() {
            return $this->rendered;
        }
		
		public function render() {
			$page = CPage::instance();
			echo $page->render($this);
		}

        

       

        public function user() {
            if ($this->_user == null) {
                $session = Session::instance();
                $user = $session->get("user");
                if (!$user) $user = null;
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

        public function org_id() {
            $org = $this->org();
			if($org==null) return null;
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
                    $this->_store = cstore::get(CF::org_code(), CF::store_code());
                }
            }
            return $this->_store;
        }
		
		public function store_id() {
            $store = $this->store();
			if($store==null) return null;
			return $store->store_id;
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
                if (count($childs) > 0) $result = array_merge($result, $childs);
            }
            return $result;
        }

        public function get_role_child_list($role_id = null) {
            if (strlen($role_id) == 0) $role_id = $this->role()->role_id;
            $child_array = $this->get_child_array($role_id);
            $child_list = array();


            foreach ($child_array as $child) {

                $child_list[$child["id"]] = cutils::indent($child["level"], "&nbsp;&nbsp;&nbsp;&nbsp;") . $child["name"];
            }
            return $child_list;
        }

        public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL) {

            try {
                $app = MApp::instance();
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
                }
                else {
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
                    }
                    else {
                        $level = 1;
                        $error = $PHP_ERROR ? 'Unknown Error' : get_class($exception);
                        $description = '';
                    }
                }
                else {
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
                }
                else {
                    CF::exception_handler($exception, $message, $file, $line);
                }
            }
            catch (Exception $e) {
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
            if (ccfg::get("minify_js")) {
                $js = CJSMin::minify($js);
            }
            $data["js"] = cbase64::encode($js);
            $data["css_require"] = CClientScript::instance()->url_css_file();
            return cjson::encode($data);
        }
        public function is_mobile() {
            return $this->mobile;
        }
    }
    