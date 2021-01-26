<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Account extends CController {

    public function index() {
        $this->profile();
    }

    public function edit_profile() {
        $app = CApp::instance();
        $app->title(clang::__('Edit Profile'));
        $app->show_title(true);
        $app->show_breadcrumb(true);
        $org = $app->org();
        csess::refresh_user_session();
        $db = CDatabase::instance();
        $role = $app->role();

        $user = $app->user();
        if ($user == null) {
            echo $app->render();
            return;
        }
        $user_id = $user->user_id;
        $post = $this->input->post();
        if ($post != null) {
            $error = 0;
            $error_message = "";
            $db->begin();
            if ($error == 0) {
                try {
                    $first_name = $post["first_name"];
                    $last_name = $post["last_name"];
                    $data = array(
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                    );
                    $db->update("users", $data, array("user_id" => $user_id));
                } catch (Exception $ex) {
                    $error++;
                    $error_message = "Error, call administrator..." . $ex->getMessage();
                }
            }
            if ($error == 0) {
                //do save file image
                $filename = "";
                if (isset($_FILES["user_photo"])) {
                    $filename = $_FILES["user_photo"]["name"];
                }

                $filename = cutils::sanitize($filename, true);

                if (strlen($filename) > 0) {
                    cimage::create_image_folder("user_photo", $user_id);

                    cimage::delete_all_image("user_photo", $user_id, $user->user_photo);

                    $filename = cupload::save("user_photo", $filename, cimage::get_image_path('user_photo', $user_id, 'original'));

                    cimage::resize_image("user_photo", $user_id, $filename);

                    $db->update("users", array("user_photo" => basename(stripslashes($filename))), array("user_id" => $user_id));
                }
            }

            if ($error == 0) {
                $db->commit();

                csess::refresh_user_session();

                cmsg::add("success", "Success update your profile !");

                curl::redirect("account/profile");
            } else {
                $db->rollback();
                cmsg::add("error", $error_message);
            }
        }
        $form = $app->add_form()->set_enctype('multipart/form-data');
        $widget = $form->add_widget();


        $user = cuser::get($user_id);
        $widget->set_title(clang::__('Edit Profile'))->set_icon('user');
        $div = $widget->add_div()->add_class('row-fluid');
        $span2 = $div->add_div()->add_class('span2')->add_class('align-center');
        $user_photo = $user->user_photo;
        $imgsrc = curl::base() . 'cresenity/noimage/120/120';
        if (strlen($user_photo) > 0) {
            $imgsrc = cimage::get_image_src("user_photo", $user_id, "small", $user_photo);
        }

        $span2->add_control('user_photo', 'image')->set_imgsrc($imgsrc)->set_maxwidth(120)->set_maxheight(120);
        $span2->add_div()->custom_css('height', '30px');
        $span10 = $div->add_div()->add_class('span10');
        $div = $span10->add_div()->add_class('row-fluid');
        $span = $div->add_div()->add_class('span6');
        $span->add_field()->set_label(clang::__("Role"))->add_control('role', 'label')->add_validation(null)->set_value($role->name);
        $span->add_field()->set_label(clang::__("Username"))->add_control('username', 'label')->add_validation(null)->set_value($user->username);
        $span->add_field()->set_label(clang::__("First Name"))->add_control('first_name', 'text')->add_validation(null)->set_value($user->first_name);
        $span->add_field()->set_label(clang::__("Last Name"))->add_control('last_name', 'text')->add_validation(null)->set_value($user->last_name);
        $span = $div->add_div()->add_class('span6');
        $span->add_field()->set_label(clang::__("Last Login"))->add_control('last_login', 'label')->add_validation(null)->set_value($user->last_login);
        $span->add_field()->set_label(clang::__("Last Request"))->add_control('last_request', 'label')->add_validation(null)->set_value($user->last_login);

        $actions = $widget->add_action_list();
        $actions->set_style('form-action');
        $actions->add_action('submit')->set_label('Submit')->set_submit(true);

        $app->add_breadcrumb(clang::__("My Profile"), curl::base() . "account/profile");
        echo $app->render();
    }

    public function profile($user_id = "") {
        $app = CApp::instance();
        $app->title(clang::__('My Profile'));
        $app->show_title(true);
        $app->show_breadcrumb(true);
        $org = $app->org();
        $role = $app->role();
        csess::refresh_user_session();
        $db = CDatabase::instance();

        $login_user = $app->user();
        $login_user_id = $login_user->user_id;
        if (strlen($user_id) == 0) {
            $user = $login_user;
            $user_id = $login_user_id;
        }
        $form = $app->add_form();
        $widget = $form->add_widget();
        if ($user_id == $login_user_id) {
            $actadd = $widget->add_header_action('edit-profile')->set_label(' ' . clang::__('Edit Profile'));
            $actadd->set_icon("pencil")->set_link(curl::base() . "account/edit_profile/");
        }

        $user = cuser::get($user_id);
        $widget->set_title(clang::__('My Profile'))->set_icon('user');
        $div = $widget->add_div()->add_class('row-fluid');
        $span2 = $div->add_div()->add_class('span2')->add_class('align-center');
        $user_photo = $user->user_photo;
        $imgsrc = curl::base() . 'cresenity/noimage/120/120';
        if (strlen($user_photo) > 0) {
            $imgsrc = cimage::get_image_src("user_photo", $user_id, "small", $user_photo);
        }

        $span2->add('<img class="profile-img" src="' . $imgsrc . '">');
        $span2->add_div()->custom_css('height', '30px');
        $span10 = $div->add_div()->add_class('span10');
        $div = $span10->add_div()->add_class('row-fluid');
        $span = $div->add_div()->add_class('span6');
        $span->add_field()->set_label(clang::__("Role"))->add_control('role', 'label')->add_validation(null)->set_value($role->name);
        $span->add_field()->set_label(clang::__("Username"))->add_control('username', 'label')->add_validation(null)->set_value($user->username);
        $span->add_field()->set_label(clang::__("First Name"))->add_control('first_name', 'label')->add_validation(null)->set_value($user->first_name);
        $span->add_field()->set_label(clang::__("Last Name"))->add_control('last_name', 'label')->add_validation(null)->set_value($user->last_name);
        $span = $div->add_div()->add_class('span6');
        $span->add_field()->set_label(clang::__("Last Login"))->add_control('last_login', 'label')->add_validation(null)->set_value($user->last_login);
        $span->add_field()->set_label(clang::__("Last Request"))->add_control('last_request', 'label')->add_validation(null)->set_value($user->last_login);
        $span->add_field()->set_label(clang::__("Hit Count"))->add_control('hit_count', 'label')->add_validation(null)->set_value(cuser::hit_count($user->user_id))->add_transform('thousand_separator');

        $widget = $app->add_widget()->set_nopadding(true)->set_title(clang::__('My Last Activity'));
        $table = $widget->add_table();
        $table->set_title('My Last Activity');
//            $q = "select * from log_activity order by activity_date desc limit 10";
        $q = "SELECT la.*, u.org_id FROM log_activity AS la RIGHT JOIN users AS u ON la.user_id = u.user_id
			  WHERE u.user_id = '$user_id' ORDER BY la.activity_date DESC LIMIT 10";
//            $q = "SELECT
//                    la.*,
//                    u.username
//                    FROM
//                    log_activity AS la
//                    INNER JOIN users AS u ON la.user_id = u.user_id
//                    ORDER BY
//                    la.activity_date DESC
//                    limit 10";
        $table->set_data_from_query($q);
        $table->add_column('activity_date')->set_label("Activity Date");
        $table->add_column('routed_uri')->set_label("URL");
        $table->add_column('description')->set_label("Description");
        $table->set_apply_data_table(false);

        echo $app->render();
    }

    public function change_password() {
        $app = CApp::instance();
        $app->title(clang::__("Change Password"));
        $user = $app->user();
        $post = $this->input->post();
        $db = CDatabase::instance();
        $current_password = "";
        $password = "";
        $confirmation = "";

        if ($post != null) {

            $error = 0;
            $error_message = "";
            try {
                if (isset($post["current_password"])) {
                    $current_password = $post["current_password"];
                }
                $password = $post["password"];
                $confirmation = $post["confirmation"];

                if ($error == 0) {
                    if ($password != $confirmation) {
                        $error++;
                        $error_message = "New Password Not Match";
                    }
                }

                if ($error == 0) {
                    $q = "select * from users where user_id=" . $db->escape($user->user_id) . " and password=md5(" . $db->escape($current_password) . ");";
                    $r = $db->query($q);
                    if ($r->count() == 0) {
                        $error++;
                        $error_message = "Old Password Wrong";
                    }
                }

                if ($error == 0) {
                    $data = array(
                        "password" => md5($password),
                    );
                }

                //checking
                if ($error == 0) {
                    $db->update("users", $data, array("user_id" => $user->user_id));
                }
            } catch (Exception $e) {
                $error++;
                $error_message = "Error, call administrator..." . $e->getMessage();
                ;
            }
            if ($error == 0) {
                cmsg::add("success", "Password \"" . $user->username . "\" Successfully Modified !");
                curl::redirect("home");
            } else {
                cmsg::add("error", $error_message);
            }
        }
        $html = '';



        $form = $app->add_form();
        $widget = $form->add_widget();
        $widget->set_title(clang::__('Change Password'))->set_icon('key');
        $span = $widget->add_span()->set_col(5);
        $span->add_field()->set_label('Password')->add_control('current_password', 'password')->add_validation("required")->set_value($current_password);
        $span->add_field()->set_label('New Password')->add_control('password', 'password')->add_validation("required")->set_value($password);
        $span->add_field()->set_label('Confirmation')->add_control('confirmation', 'password')->add_validation("required")->set_value($confirmation);
        $actions = $widget->add_action_list();
        $actions->set_style('form-action');
        $act_next = $actions->add_action()->set_label('Submit')->set_submit(true);

        echo $app->render();
    }

}

// End Home Controller