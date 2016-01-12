<?php

$app = CApp::instance();
$org = $app->org();
$user = $app->user();
$org_id = $org->org_id;
$db = CDatabase::instance();

$users = cuser::get($user_id);
//$v = users::activity($user_id);
//$v = CView::factory('customer/detail/html');
//$v->customer_id = $sales->customer_id;
//echo $v->render();

csess::refresh_user_session();

$form = $app->add_form();
$widget = $form->add_widget();
$widget = $app->add_widget()->set_nopadding(true)->set_title(clang::__('My Last Activity'));
$table = $widget->add_table();
$table->set_title('My Last Activity');
$q = "select * from log_activity order by activity_date desc limit 10 where user_id=" . $users;
$table->set_data_from_query($q);
$table->add_column('activity_date')->set_label("Activity Date");
$table->add_column('description')->set_label("Description");
$table->set_apply_data_table(false);

echo $app->render();
?>
