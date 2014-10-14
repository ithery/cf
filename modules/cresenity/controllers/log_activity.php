<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Log_activity_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Activity Log"));
        $user = $app->user();
        $org = $app->org();
        $org_id = "";
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $user_id = "";
        $app_id = "";
        $date_start = cutils::begin_date_month();
        $date_end = cutils::last_date_month();
        $activity_type = "";
        $request = $_GET;
        $db = CDatabase::instance();

        $redirect_to = "";
        $download = 0;
        if (isset($request["download"])) {
            $download = $request["download"];
        }


        if (isset($request["user_id"]))
            $user_id = $request["user_id"];
        if (isset($request["app_id"]))
            $app_id = $request["app_id"];
        if (isset($request["activity_type"]))
            $activity_type = $request["activity_type"];
        if (isset($request["date_start"]))
            $date_start = $request["date_start"];
        if (isset($request["date_end"]))
            $date_end = $request["date_end"];

        $app_list = cdbutils::get_list("select a.app_id,a.name as name from app a order by a.name asc;");
        $app_list = array("ALL" => "ALL") + $app_list;
        $user_list = cdbutils::get_list("select u.user_id,concat('[',a.name,'] ',u.username) as name from roles a inner join users u on u.role_id=a.role_id where u.status>0 and u.org_id=" . $db->escape($org_id) . " order by a.role_id,u.user_id asc;");
        $user_list = array("ALL" => "ALL") + $user_list;

        $form = $app->add_form('log_activity_form')->set_method("get");
        $widget = $form->add_widget();
        $widget->set_title(clang::__("Filter"));
        $widget->set_icon('filter');

        $widget->add_field('app-field')->set_label(clang::__("Application"))->add_control('app_id', 'select')->add_validation(null)->set_value($app_id)->set_list($app_list);
        $widget->add_field('user-field')->set_label(clang::__("User"))->add_control('user_id', 'select')->add_validation(null)->set_value($user_id)->set_list($user_list);

        $activity_type_list = array(
            "delete" => "Delete",
        );
        $activity_type_list = array("ALL" => "ALL") + $activity_type_list;
        $widget->add_field('activity_type-field')->set_label(clang::__("Activity Type"))->add_control('activity_type', 'select')->add_validation(null)->set_value($activity_type)->set_list($activity_type_list);
        $widget->add_field('date-start-field')->set_label(clang::__("Date Start"))->add_control('date_start', 'date')->add_validation(null)->set_value($date_start);
        $widget->add_field('date-end-field')->set_label(clang::__("Date End"))->add_control('date_end', 'date')->add_validation(null)->set_value($date_end);
        $widget->add_control('download', 'hidden')->set_value("0");
        $widget->add_control('submitted', 'hidden')->set_value("1");

        $actions = CActionList::factory('act_roles');

        $act_submit = CAction::factory('submit_button')->set_label(clang::__("Submit"))->set_icon("ok")->set_submit(true);
        $act_download = CAction::factory('download_button')->set_label(clang::__("Download"))->set_icon("download")->set_jsfunc("void(0)");
        $actions->add($act_submit);
        if (cnav::have_permission('download_xls_log_activity')) {
            $actions->add($act_download);
        }

        $widget->add($actions);

        $additional_js = "
			
				jQuery(document).ready(function() {
					$('#submit_button').click(function(event) {
						event.preventDefault();
						$('#download').val('0');
						$('#log_activity_form').submit();
					});
					$('#download_button').click(function(event) {
						event.preventDefault();
						$('#download').val('1');
						$('#log_activity_form').submit();
					});
				});
		
		";
        $app->add_js($additional_js);


        //if submitted
        if (isset($request["submitted"])) {
            $q = ' 
				select 
					l.log_activity_id
					,u.username
					,a.name as app_name
					,l.remote_addr
					,l.user_agent
					,l.activity_type
					,l.activity_date
					,l.description
				from 
					log_activity l 
					inner join app a on l.app_id=a.app_id 
					inner join users u on l.user_id=u.user_id 
				where u.status>0 
			';

            $q.= ' and u.org_id=' . $db->escape($org_id);

            if ($app_id != "" && $app_id != "ALL") {
                $q.=" and a.app_id=" . $db->escape($app_id);
            }
            if ($user_id != "" && $user_id != "ALL") {
                $q.=" and u.user_id=" . $db->escape($user_id);
            }
            if ($activity_type != "" && $activity_type != "ALL") {
                $q.=" and l.activity_type=" . $db->escape($activity_type);
            }
            if ($date_start != "" && $date_start != "ALL") {
                $q.=" and l.activity_date>=" . $db->escape($date_start . ' 00:00:00');
            }
            if ($date_end != "" && $date_end != "ALL") {
                $q.=" and l.activity_date<=" . $db->escape($date_end . ' 23:59:59');
            }



            $q.= " order by l.activity_date desc limit 1000";

            $table = CTable::factory('report_log_activity');
            $table->add_column('activity_date')->set_label(clang::__("Date"));

            $table->add_column('username')->set_label(clang::__("Username"));
            $table->add_column('activity_type')->set_label(clang::__("Activity Type"))->add_transform('uppercase');
            $table->add_column('description')->set_label(clang::__("Description"));




            $table->set_data_from_query($q)->set_key('log_activity_id');
            $table->set_title(clang::__("Log Activity"));
            $table->cell_callback_func(array("Log_activity_Controller", "cell_callback"));
            $table->set_apply_data_table(false);



            if ($download == 1) {
                $filename = date("YmdHis");
                $rand = rand(0, 89999);
                $rand = 10000 + $rand;
                $filename = $rand . $filename . "-LOG_ACTIVITY-CRESENITY_APP" . ".xls";
                $table->export_excel($filename, "Log_Activity");
            } else {
                $app->add($table);
            }
        }


        echo $app->render();
    }

    public static function cell_callback($table, $col, $row, $text) {

        return $text;
    }

}

// End Welcome Controller