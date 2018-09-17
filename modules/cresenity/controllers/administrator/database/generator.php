<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 8:37:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Database_Generator extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Database Generator"));

        $db = CDatabase::instance();

        $post = $_POST;
        if ($post != null) {
            if (isset($post['submit'])) {
                $table_info = cdbutils::get_table_info();
                $table_info_path = DOCROOT;

                $table_info_path = $table_info_path . "db" . DIRECTORY_SEPARATOR;
                if (!is_dir($table_info_path))
                    mkdir($table_info_path);
                $table_info_path = $table_info_path . "structure" . DIRECTORY_SEPARATOR;
                if (!is_dir($table_info_path))
                    mkdir($table_info_path);
                $table_info_path = $table_info_path . "stable" . DIRECTORY_SEPARATOR;
                if (!is_dir($table_info_path))
                    mkdir($table_info_path);
                $table_info_file = $table_info_path . "tables.php";
                cphp::save_value($table_info, $table_info_file);
                foreach ($table_info as $k => $v) {
                    $table_name = $k;
                    $column_info_path = $table_info_path . "tables" . DIRECTORY_SEPARATOR;
                    if (!is_dir($column_info_path))
                        mkdir($column_info_path);
                    $column_info_file = $column_info_path . $table_name . ".php";
                    $column_info = cdbutils::get_column_info($table_name);
                    cphp::save_value($column_info, $column_info_file);
                }
            }
        }

        $table = "";

        $widget = $app->add_widget();
        $form = $widget->add_form();
        $table_list = cdbutils::get_table_list();
        $table_list = array('' => 'Please Select...') + $table_list;
        $form->add_field()->set_label("Table Count")->add_control('table', 'label')->set_value(cdbutils::get_table_count());
        $form->add_action_list()->add_action('submit')->set_submit(true)->set_label('Submit');

        //->set_applyjs(false);
        //$div = $form->add_div()->add_class('pos_bill_container')->add_div('column_string')->add_class('pos_bill_lx300_container_inner');


        $js = "
			jQuery(document).ready(function() {
				jQuery('#table').on('change',function() {
					jQuery.ajax({
						type: 'get',
						url: '" . curl::base() . "admin/db_column_generator/column_string/'+jQuery('#table').val(),
						dataType: 'text',
						data: {
							
						}
					}).done(function( data ) {
						
						jQuery('#column_string').html(data);
						
					}).error(function(obj,t,msg) {
						
						$.cresenity.message('error','Error, please call administrator... (' + msg + ')');

					});
					
				});
			});
		";

        echo $app->render();
    }

    public function column_string($table) {
        $column_info = cdbutils::get_column_info($table);

        echo cphp::string_value($column_info);
    }

    public function table_string() {
        $column_info = cdbutils::get_table_info();

        echo cphp::string_value($column_info);
    }

}
