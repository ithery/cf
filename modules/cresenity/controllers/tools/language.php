<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Feb 29, 2016
     */
    class Controller_Tools_Language extends CController {

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

    }
    