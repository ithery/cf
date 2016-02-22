<?php defined('SYSPATH') OR die('No direct access allowed.');
	class CPage {
		protected $header;
		protected $footer;
		protected $rendered;
		protected $mobile;
		protected $set_is_login_page;
		protected $header_body;
		protected $title;
		protected $page_title;
		protected $custom_js;
		protected $custom_header;
		protected $custom_footer;
		protected $show_breadcrumb;
		protected $show_title;
		protected $breadcrumb;
		protected $additional_head;
		protected $load_client_script;
		
		
		
		
		private static $instance;
		public function __construct() {
			$this->rendered = false;
			$this->header_body = '';
			$this->title = '';
			$this->custom_js = '';
			$this->custom_header = '';
			$this->custom_footer = '';
			$this->load_client_script = '';
			$this->show_breadcrumb = false;
			$this->show_title = false;
			$this->mobile = ccfg::get('is_mobile');
			$this->header = CPage_Header::instance();
			$this->footer = CPage_Footer::instance();
		}
		
		public static function instance() {
			if(self::$instance==null) {
				self::$instance = new CPage();
			}
			return self::$instance;
		}
		
		public function set_title($title) {
            $this->title = clang::__($title);
			if(strlen($this->page_title)==0) {
				$this->set_page_title(clang::__($title));
			}
            return $this;
        }
		public function set_page_title($title) {
			$this->page_title = clang::__($title);
			return $this;
		}
		public function set_header_body($header_body) {
            $this->header_body = $header_body;
            return $this;
        }
		
		public function render($app) {
			if ($this->rendered) {
                echo 'a';
				//trigger_error('CPage already rendered');
            }
			$this->rendered = true;
            if (crequest::is_ajax()) {
                return $this->json();
            }
			
			$theme_path = '';
            $theme = ccfg::get('theme');
            if ($theme == null) $theme = 'cresenity';
            $theme_file = CF::get_file('themes', $theme);
            $page_var = array();
			$theme_custom_js = '';
            if (file_exists($theme_file)) {
                $theme_data = include $theme_file;
                $theme_path = carr::get($theme_data, 'theme_path');
                $page_var = carr::get($theme_data, 'page_var');
                $theme_custom_js = carr::get($theme_data, 'custom_js');
                if ($theme_path == null) {
                    $theme_path = '';
                }
                else {
                    $theme_path .= '/';
                }
				
            }

            
            if (!$app->is_user_login() && ccfg::get("have_user_login") && $this->login_required) {
                $v = CView::factory($theme_path . 'ccore/login');
            }
            else if (!$app->is_user_login() && ccfg::get("have_static_login") && $this->login_required) {
                $v = CView::factory($theme_path . 'ccore/static_login');
            }
            else {
                
                $v = CView::factory($theme_path . 'cpage');

                $this->content = $app->html();
                $this->js = $app->js();

                $v->content = $this->content;
                $v->header_body = $this->header_body;

                $v->title = $this->title;
                $cs = CClientScript::instance();
				$cm = CClientModules::instance();
                $css_urls = $cs->url_css_file();

                $js_urls = $cs->url_js_file();
                $additional_js = "";

                foreach ($css_urls as $url) {

                    $additional_js .= "
					$.cresenity._filesadded+='['+'" . $url . "'+']'
				";
                }
                $js = "";
				
                $vjs = CView::factory('ccore/js');
				if(CView::exists($theme.'/ccore/js')) {
					
					$vjs= CView::factory($theme.'/ccore/js');
				}
                $js.=PHP_EOL . $vjs->render();

				
				if(strlen($theme_custom_js)>0) {
					$this->js.=$theme_custom_js;
				}
                $js .= PHP_EOL . $this->js . $additional_js;

                //$js = $cs->render_js_require($js);

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
                $v->head_client_script = carr::get($page_var, 'head_client_script','');
				
                $v->begin_client_script = carr::get($page_var, 'begin_client_script','');
                $v->end_client_script = carr::get($page_var, 'end_client_script','');

                $v->load_client_script = carr::get($page_var, 'load_client_script','');
                $v->ready_client_script = carr::get($page_var, 'ready_client_script','');


                $v->head_client_script .= $cs->render('head');
                $v->begin_client_script .= $cs->render('begin');
                // $v->end_client_script = $cs->render('end');

                $v->load_client_script .= $cs->render('load');
                $v->ready_client_script .= $cs->render('ready');

                $v->custom_js = $this->custom_js;
                $v->custom_header = $this->custom_header;
                $v->custom_footer = $this->custom_footer;
                $v->show_breadcrumb = $this->show_breadcrumb;
                $v->show_title = $this->show_title;
                $v->breadcrumb = $this->breadcrumb;
				$v->additional_head=$this->additional_head;
			}
			return $v->render();
		}
	}