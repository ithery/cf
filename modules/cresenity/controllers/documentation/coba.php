<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Coba_Controller extends CController {

    public function nestable() {
        $db = CDatabase::instance();
        $app = CApp::instance();
        $tree = CTreeDB::factory('item_type');
        $widget = $app->add_widget()->set_title(clang::__("Coba"));
        $nestable = $widget->add_nestable();
        $widget->clear_both();
        $nestable->set_data_from_treedb($tree)->set_id_key('item_type_id')->set_value_key('name')->set_input('data_order');

        $nestable->set_applyjs(false);
        $nestable->set_action_style('btn-dropdown');

        echo $app->render();
    }

    public function tabs() {
        $app = CApp::instance();
        $app->title(clang::__("Form Example"));

        $tabs = $app->add_tab_list()->set_ajax(true);
        $tabs->set_scrollspy(false);
        $tabs->add_tab('tab_1')->set_label('TAB 1')->add_field()->set_label('TEST 1');
        $tabs->add_tab('tab_2')->set_label('TAB 2')->add_widget();
        $tabs->add_tab('tab_3')->set_label('TAB 3')->add_widget()->add_form()->add_field()->set_label('TEST 3');
        $tab_4 = $tabs->add_tab('tab_4')->set_label('TAB 4');
        $reload_content = $tab_4->add_action()->set_label('Reload')->add_listener('click')->add_handler('reload')->set_target('reload_div')->content();
        $reload_content->add_widget();
        $tab_4->add_div('reload_div');
        echo $app->render();
    }

    public function forms() {
        $app = CApp::instance();
        $forms = CFormValidation::factory();
        $widget = $app->add_widget();
        $form = $widget->add_form();
        $form->add_field()->set_label('Currency')->add_control('idr', 'text')->add_validation("required");
//        $forms->min("5");
        $form->add_action("submit")->set_label('Submit')->set_submit(true);
        echo $app->render();
    }

}