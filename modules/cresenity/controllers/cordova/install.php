<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Jun 3, 2016
     */
    class Controller_Cordova_Install extends CController {
        
        public function __construct() {
            parent::__construct();
        }
        
        public function index(){
            $app = CApp::instance();
            
            
            $cordova_dir = '';
            $main_url = '';
            $url_manifest_server = '';
            $id_unique = '';
            
            $post = $_POST;
            if (isset($post) && count($post) > 0) {
                // do installing
                $cordova_dir = carr::get($post, 'cordova_dir');
                $main_url = carr::get($post, 'main_url');
                $id_unique = carr::get($post, 'id_unique');
                $url_manifest_server = carr::get($post, 'url_manifest_server');
                
                $view = CView::factory('ccore/cordova/mobile_index_js');
                $view->url = $main_url;
                $view->manifest_file_server = $url_manifest_server;
                $view->id_unique = $id_unique;
                $mobile_js = $view->render();
                $mobile_js = str_replace('<script>', '', $mobile_js);
                file_put_contents($cordova_dir .'/www/js/index.js', $mobile_js);
                
                $view_index_html = CView::factory('ccore/cordova/index_html');
                $index_html = $view_index_html->render();
                file_put_contents($cordova_dir .'/www/index.html', $index_html);
                cmsg::add('success', clang::__('Cordova Application Successfully installed'));
            }
            
            $container = $app->add_div()->add_class('cordova-container');
            $form = $container->add_form();
            $tab_list = $form->add_tab_list()->set_ajax(false);
            $main_tab = $tab_list->add_tab('main-tab')->set_label('Main')->set_active(true);
            $index_tab = $tab_list->add_tab('index-page-tab')->set_label('Index HTML');
            
            $main_tab->add_field()->set_label('App Cordova Directory')->add_control('cordova_dir', 'text')
                    ->set_value($cordova_dir)
                    ->set_placeholder('D:\ITTRon\P.R.O.J.E.C.T\PhoneGap\MobileIntern');
            $main_tab->add_field()->set_label('Main URL')->add_control('main_url', 'text')
                    ->set_value($main_url)
                    ->set_placeholder('xxx.local');
            $main_tab->add_field()->set_label('URL Manifest Server')->add_control('url_manifest_server', 'text')
                    ->set_value($url_manifest_server)
                    ->set_placeholder('http://xxx.local/home/manifest');
            $main_tab->add_field()->set_label('ID Unique')->add_control('id_unique', 'text')
                    ->set_value($id_unique)
                    ->set_placeholder('com.appmobilezulniar.dejavamall');
            
            
            $form->add_action('install')->set_label('Install')->set_submit(true)->set_confirm(true);
            
            echo $app->render();
        }
    }
    