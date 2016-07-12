<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Feb 29, 2016
     */
    class Controller_Tools_Language extends CController {

        public function __construct() {
            parent::__construct();
        }

        public function index() {
            $app = CApp::instance();

            $container = $app->add_div();
            $form = $container->add_form();

            $language = 'en';
            $request = $_POST;
            if (isset($request)) {
                $submit = carr::get($request, 'submit');
                if (strlen($submit) > 0) {
                    $language = carr::get($request, 'language');
                    $lang_key = carr::get($request, 'lang_key');
                    $lang_val = carr::get($request, 'lang_val');

                    $file = clang::get_file($language);
                    if ($file == null) {
                        $file = clang::get_dir($language) . $language . '.php';
                    }

                    $languages = array();
                    foreach ($lang_key as $k => $v) {
                        if (strlen($k) > 0) {
                            $languages[$v] = carr::get($lang_val, $k);
                        }
                    }
                    cphp::save_value($languages, $file);
                }
            }
            $list = clang::get_lang_list();

            $select = $form->add_field()->set_label('Language')->add_control('language', 'select')
                            ->set_list($list)->set_value($language);
            $select->add_listener('change')->add_handler('reload')->set_target('lang-wrapper')
                    ->add_param_input(array('language'))
                    ->set_url(curl::base() . 'tools/language/load_language');
            $select->add_listener('ready')->add_handler('reload')->set_target('lang-wrapper')
                    ->add_param_input(array('language'))
                    ->set_url(curl::base() . 'tools/language/load_language');
//            $form->add_action_list()->add_action()->set_label(clang::__('Save'))
//                    ->add_class('btn btn-success')->set_submit(true);
            $form->add_div()->custom_css('color', 'red')
                    ->add('** Leave default blank to remove a row');
            $form->add_div('lang-wrapper');
            $form->add_control('submit', 'hidden')->set_value(true);
            $form->add_action_list()->add_action()->set_label(clang::__('Save'))
                    ->add_class('btn btn-success')->set_submit(true);
            $app->add('
                <style>
                    .show-grid {
                        padding-top: 3px;
                        padding-bottom: 3px;
                    }
                    .show-grid.title {
                        padding-top: 7px;
                        padding-bottom: 7px;
                    }
                </style>
                ');
            echo $app->render();
        }

        public function load_language() {
            $app = CApp::instance();

            $request = $_GET;
            $language = carr::get($request, 'language');

            $title = clang::get_lang_name_by_code($language);
            $widget = $app->add_widget()->set_title('Default to ' . $title);
            if ($language == 'default') {
                $widget->add('This is default language. No File detected');
            }
            else {
                $languages = array();
                $file = clang::get_file($language);
                if ($file != null) {
                    $languages = include $file;
                }
//                $widget->add($languages);
                $lang_content = $widget->add_div()->add_class('row-fluid show-grid title')
                        ->custom_css('background-color', '#757575')
                        ->custom_css('color', '#FAFAFA');
                $header_action_add = $widget->add_header_action('')->set_label('Add New Lang')
                                ->set_icon('plus')->add_class('btn-warning');
                $header_action_add->add_listener('click')->add_handler('append')
                        ->set_target('widget-container')
                        ->set_url(curl::base() . 'tools/language/add_new_row');

                $lang_content->add_div()->add_class('span5')->add('<h4>Default</h4>')
                        ->custom_css('text-align', 'center');
                $lang_content->add_div()->add_class('span1')->add('<i class="icon-arrow-right"></i>');
                $lang_content->add_div()->add_class('span6')->add('<h4>' . $title . '</h4>')
                        ->custom_css('text-align', 'center');

                $widget_container = $widget->add_div('widget-container')->add_class('widget-container');
                foreach ($languages as $language_k => $language_v) {
                    $content = $widget_container->add_div()->add_class('row-fluid show-grid');
                    $content->add_div()->add_class('span5')
                            ->add_control('', 'text')->set_name('lang_key[]')->set_value($language_k);
                    $content->add_div()->add_class('span1')->add('<i class="icon-arrow-right"></i>');
                    $content->add_div()->add_class('span6')
                            ->add_control('', 'text')->set_name('lang_val[]')->set_value($language_v);
                }

                $footer = $widget->add_div()->add_class('row-fluid');
                $btn_add = $footer->add_div()->add_class('span12')->add_action()->set_label('Add New Lang')
                                ->set_submit(false)->add_class('btn btn-warning');
                $btn_add->add_listener('click')->add_handler('append')
                        ->set_target('widget-container')
                        ->set_url(curl::base() . 'tools/language/add_new_row');
            }
            echo $app->render();
        }

        public function add_new_row($language_k = null, $language_v = null) {
            $app = CApp::instance();
            $content = $app->add_div()->add_class('row-fluid show-grid');
            $content->add_div()->add_class('span5')
                    ->add_control('', 'text')->set_name('lang_key[]')->set_value($language_k);
            $content->add_div()->add_class('span1')->add('<i class="icon-arrow-right"></i>');
            $content->add_div()->add_class('span6')
                    ->add_control('', 'text')->set_name('lang_val[]')->set_value($language_v);
            echo $app->render();
        }

        public function grab_lang() {
            $app = CApp::instance();

            $app_path = APPPATH;

            $dirs = array();
            $files = glob($app_path .'*');
            foreach($files as $file) {
                if (is_dir($file)) {
                    $name = str_replace($app_path, '', $file);
                    $dirs[$name] = $name;
                }
            }
            
            $form = $app->add_form();
            $form->add_field()->set_label('Set Application')
                    ->add_control('application', 'select')->set_list($dirs);
            $form->add_action_list()->add_action()
                    ->set_label('Show')->add_class('btn-primary')->add_listener('click')->add_handler('reload')
                    ->set_url(curl::base() .'tools/language/load_lang_list')
                    ->set_target('lang-container')->add_param_input(array('application'));
            
            $app->add_div('lang-container');
            echo $app->render();
        }
        
        public function load_lang_list(){
            $app = CApp::instance();
            
            $request = $_GET;
            $err_code = 0;
            $err_message = '';
            
            $application = carr::get($request, 'application');
            if (strlen($application) == 0) {
                $err_code++;
                $err_message = clang::__('Application invalid');
            }
            $app_path = APPPATH .$application;
            if (!is_dir($app_path)) {
                $err_code++;
                $err_message = clang::__('Application not found');
            }
            
            if ($err_code == 0) {
                $widget = $app->add_widget()->set_title(clang::__('List of Language'));
                $files = array();
                $ignore_dirs = array('.git', '.gitignore', 'logs', 'resources', 'media', 'nbproject');
                cfs::list_files_in_dir($app_path, $files, $ignore_dirs);
                
                foreach ($files as $key => $file) {
                    $content = @file_get_contents($file);
                    if ($content != false) {
                        $langs = array();
                        preg_match_all('#clang::__\((.+?)\)#ims', $content, $langs, PREG_SET_ORDER);
                        
                        $have_lang = false;
                        if (count($langs) > 0) {
                            $have_lang = true;
                        }
                        if ($have_lang) {
                            $widget_lang = $widget->add_widget()->set_title($file)->set_collapse(true);
                            $data_table = array();
                            $table = $widget_lang->add_table();
                            $table->set_apply_data_table(false);
                            $table->add_column('def_lang')->set_label(clang::__('Default Language'));
                            $table->add_column('error')->set_label(clang::__('ERROR'));
                        }
                        foreach ($langs as $lang_key => $lang_val) {
                            if (isset($lang_val[1])) {
                                // remove quote or double quotes at prefix and suffix
                                $prefix = substr($lang_val[1], 0, 1);
                                $suffix = substr($lang_val[1], strlen($lang_val[1]) - 1, 1);
                                $def_lang = $lang_val[1];
                                if ($prefix == '"' || $prefix == "'") {
                                    $def_lang = substr($def_lang, 1, strlen($def_lang));
                                }
                                if ($suffix == '"' || $suffix == "'") {
                                    $def_lang = substr($def_lang, 0, strlen($def_lang) - 1);
                                }
                                
                                $prefix = substr($def_lang, 0, 1);
                                $suffix = substr($def_lang, strlen($def_lang) - 1, 1);
                                $messages = array();
                                if ($prefix == ' ' || $suffix == ' ') {
                                    $messages[] = '<span class="label label-danger">WHITESPACE</span>';
                                }
                                
                                preg_match('#\$.*\s?#ims', $def_lang, $php_var);
                                if (count($php_var) > 0) {
                                    $messages[] = '<span class="label label-danger">PHPVAR</span>';
                                }
                                
                                preg_match('#<.+?>?#ims', $def_lang, $html_tag);
                                if (count($html_tag) > 0) {
                                    $messages[] = '<span class="label label-danger">HTMLTAG</span>';
                                }
                                
                                $row = CTableRow::factory();
                                
                                $div = CFactory::create_div()->add(htmlspecialchars($def_lang));
                                $row->add_column($div);
                                $div = CFactory::create_div()->add(implode(' ', $messages));
                                $row->add_column($div);
                                $data_table[] = $row;
                            }
                        }
                        if ($have_lang) {
                            $table->set_data_from_array($data_table);
                        }
                    }
                }
            }
            
            
            echo $app->render();
        }

    }
    