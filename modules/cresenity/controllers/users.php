<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Users_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Users"));
        $org = $app->org();
        $org_id = "";
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $role = $app->role();
        $actions = $app->add_div()->add_class('row-fluid')->add_div()->add_class('span12')->add_action_list();
        $actions->add_action()->set_label(" " . clang::__("Add") . " " . clang::__("Users"))->set_icon("plus")->set_link(curl::base() . "users/add/");

        $db = CDatabase::instance();
        $table = $app->add_table('users_table');
        $table->add_column('role_name')->set_label('Role Name');
        $table->add_column('username')->set_label('Username');
        $table->add_column('description')->set_label('Description');
        $table->add_column('created')->set_label('Created')->set_editable(false);
        $table->add_column('createdby')->set_label('Created By')->set_editable(false);
        $q = "select " .
                " u.user_id" .
                " ,r.role_id" .
                " ,r.name as role_name" .
                " ,u.username" .
                " ,u.description" .
                " ,u.created" .
                " ,u.createdby" .
                " from users u inner join roles as r on u.role_id=r.role_id where u.status>0 and r.role_level>" . $role->role_level;
        if(strlen($org_id)>0) {
			$q.= ' and u.org_id=' . $db->escape($org_id);
		}
        $table->set_data_from_query($q)->set_key('user_id');
        //$table->add_row_action('edit')->set_label('Edit')->set_icon('pencil');
        //$table->add_row_action('delete')->set_icon('trash');
        $table->set_action_style("btn-dropdown");
        $table->set_title(clang::__('Users'));

        $actedit = $table->add_row_action();
        $actedit->set_label("")->set_icon("search")->set_link(curl::base() . "users/detail/{param1}")->set_confirm(false)->set_label(clang::__('Detail User'));
        $actedit = $table->add_row_action('edit');
        $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . "users/edit/{param1}")->set_label(clang::__('Edit User'));
        $actedit = $table->add_row_action('delete');
        $actedit->set_label("")->set_icon("trash")->set_link(curl::base() . "users/delete/{param1}")->set_confirm(true)->set_label(clang::__('Delete User'));

        echo $app->render();
    }

    public function add() {
        $this->edit();
    }

    public function edit($id = "") {
        $app = CApp::instance();
        $title = clang::__("Edit") . " " . clang::__("Users");
        $icon = "pencil";
        if (strlen($id) == 0) {
            $title = clang::__("Add") . " " . clang::__("Users");
            $icon = "plus";
        }
        $app->title($title);
        $app->add_breadcrumb(clang::__("Users"), curl::base() . "users");
        $session = Session::instance();
        $post = $this->input->post();
        $db = CDatabase::instance();
        $is_add = 0;
        if (strlen($id) == 0) {
            $is_add = 1;
        }
        $org = $app->org();
        $org_id = "";
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $user = $app->user();
        $role = $app->role();
        $action = $is_add == 0 ? "edit" : "add";
        $username = "";
        $description = "";
        $role_id = "";
        $store_id = array();
        if ($post != null) {

            $error = 0;
            $error_message = "";
            try {

                $username = $post["username"];
                $password = $post["password"];
                $role_id = $post["role_id"];
                $description = $post["description"];
                //checking
                if ($error == 0) {
                    if (strlen($username) == 0) {
                        $error_message = clang::__("Username") . " " . clang::__("is required");
                        $error++;
                    }
                }

                if ($error == 0) {
                    if ($is_add == 1) {
                        if (strlen($password) == 0) {
                            $error_message = clang::__("Password") . " " . clang::__("is required");
                            $error++;
                        }
                    }
                }



                if ($error == 0) {
                    $qcheck = "select * from users where username=" . $db->escape($username) . " and status>0 and org_id=" . $db->escape($org_id);
                    if ($is_add == 0)
                        $qcheck .= " and user_id<>" . $db->escape($id) . "";
                    $rcheck = $db->query($qcheck);
                    if ($rcheck->count() > 0) {
                        $error_message = clang::__("Username is already exist, please try another name");
                        $error++;
                    }
                }


                if ($error == 0) {
                    $data = array(
                        "username" => $username,
                        "org_id" => $org_id,
                        "role_id" => $role_id,
                        "description" => $description,
                    );
                    if (strlen($password) > 0) {
                        $data = array_merge($data, array(
                            "password" => md5($password)
                        ));
                    }
                    if (strlen($id) == 0) {
                        $data = array_merge($data, array(
                            "created" => date("Y-m-d H:i:s"),
                            "createdby" => $user->username,
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => $user->username,
                        ));
                        $r = $db->insert("users", $data);
                        $user_id = $r->insert_id();
                    } else {
                        $data = array_merge($data, array(
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => $user->username
                        ));
                        $db->update("users", $data, array("user_id" => $id));
                        $user_id = $id;
                    }
                }
                if ($error == 0) {
                    $db->query("delete from users_store where user_id=" . $db->escape($user_id) . " and org_id=" . $db->escape($org_id));
                    if (isset($post["store_id"])) {
                        $store_id = $post["store_id"];

                        if (!is_array($store_id))
                            $store_id = array($store_id);
                        foreach ($store_id as $sid) {
                            $data = array(
                                "user_id" => $user_id,
                                "store_id" => $sid,
                                "org_id" => $org_id,
                                "created" => date("Y-m-d H:i:s"),
                                "createdby" => $user->username,
                            );
                            $db->insert("users_store", $data);
                        }
                    }
                }
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = clang::__("Error, call administrator...") . $e->getMessage();
                ;
            }
            if ($error == 0) {
                if ($id > 0) {
                    cmsg::add("flash_success", clang::__("User") . " \"" . $username . "\" " . clang::__("Successfully Modified"));
                } else {
                    cmsg::add("success", clang::__("User") . " \"" . $username . "\" " . clang::__("Successfully Added"));
                }
                curl::redirect("users");
            } else {
                cmsg::add("error", $error_message);
            }
        } else if (strlen($id) > 0) {
            $q = "select " .
                    " v.username" .
                    " ,v.role_id" .
                    " ,v.password" .
                    " ,v.description" .
                    " " .
                    " from users v where v.user_id = " . $db->escape($id);
            $result = $db->query($q);
            if ($result->count() > 0) {
                $row = $result[0];
                $username = $row->username;
                $role_id = $row->role_id;
                $password = $row->password;
                $description = $row->description;

                $store_id = cdbutils::get_array("select store_id from users_store where user_id=" . $db->escape($id));
            }
        }

        $html = '';


        $widget = $app->add_widget();


        $widget->set_title($title)->set_icon($icon);
        //$role_list = cdbutils::get_list("select role_id as k,concat(name) as v from roles where org_id=".$db->escape($org_id)."order by role_id asc,name asc;");
        $role_list = $app->get_role_child_list();
        $form = $widget->add_form();
        $form->add_field('role-field')->set_label(clang::__('Role'))->add_control('role_id', 'select')->add_validation(null)->set_value($role_id)->set_list($role_list);
        $form->add_field('username-field')->set_label(clang::__('Username'))->add_control('username', 'text')->add_validation('required')->set_value($username);
        $form->add_field('password-field')->set_label(clang::__('Password'))->add_control('password', 'password')->add_validation(null)->set_value('');

        $form->add_field('description-field')->set_label(clang::__('Description'))->add_control('description', 'textarea')->add_validation(null)->set_value($description);
        $form->add_control('id', 'hidden')->add_validation(null)->set_value($id);
        $actions = $form->add_action_list();
        $actions->set_style('form-action');
        $act = $actions->add_action();
        $act->set_label(clang::__('Submit'))->set_submit(true);


        echo $app->render();
    }

    public function detail($user_id, $method = "") {
        if ($user_id == "tab")
            return $this->tab($method);

        $user = cuser::get($user_id);
        if ($user == null)
            curl::redirect('users');

        $app = CApp::instance();
        $app->title(clang::__("User Detail"));
        $app->add_breadcrumb(clang::__("User"), curl::base() . "users");
        $org = $app->org();
        if ($org != null) {
            $login_user = $app->user();
            $login_user_id = $login_user->user_id;
            if (strlen($user_id) == 0) {
                $user = $login_user;
                $user_id = $login_user_id;
            }
            $widget = $app->add_widget()->set_title(clang::__('Users Detail'))->set_icon('search');
            $html = CView::factory('users/detail/html');
            $html->user_id = $user_id;
            $html = $html->render();
            $js = CView::factory('users/detail/js');
            $js->user_id = $user_id;
            $js = $js->render();
            $widget->add($html);
            $app->add_js($js);
        }
        echo $app->render();
    }

    public function tab($method) {
        $user_id = "";
        if (isset($_GET["user_id"]))
            $user_id = $_GET["user_id"];
        if (strlen($user_id) == 0) {
            die(clang::__("Error, No Transaction ID Passed"));
        }
        $view = CView::factory("users/detail/tab/" . $method);
        $view->user_id = $user_id;
        echo $view->render();
    }

    public function activity($user_id) {
        $app = CApp::instance();
        $org = $app->org();
        $role = $app->role();
        csess::refresh_user_session();
        $db = CDatabase::instance();

        $form = $app->add_form();
        $widget = $form->add_widget();
        $widget = $app->add_widget()->set_nopadding(true)->set_title(clang::__('My Last Activity'));
        $table = $widget->add_table();
        $table->set_title(clang::__('My Last Activity'));
        $q = "select * from log_activity order by activity_date desc limit 10 where user_id=" . $user_id;
        $table->set_data_from_query($q);
        $table->add_column('activity_date')->set_label(clang::__("Activity Date"));
        $table->add_column('description')->set_label(clang::__("Description"));
        $table->set_apply_data_table(false);
    }

    public function delete($id = "") {
        if (strlen($id) == 0) {
            curl::redirect('users');
        }
        $app = CApp::instance();
        $user = $app->user();
        $session = Session::instance();
        $db = CDatabase::instance();
        $q = '';
        $error = 0;

        $user = cuser::get($id);
        $userapp = $app->user();
        if ($error == 0) {
            if ($userapp->user_id == $user->user_id) {
                $error++;
                $error_message = clang::__("Fail on delete, you can't delete your own account...");
            }
        }
        if ($error == 0) {
            if ($user->is_base == 1) {
                $error++;
                $error_message = clang::__("Fail on delete, data is required by system...");
            }
        }
        if ($error == 0) {
            try {
                $db->update("users", array("status" => 0, "updated" => date("Y-m-d H:i:s"), "updatedby" => $userapp->username), array("user_id" => $id));
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = clang::__("Fail on delete, please call the administrator...");
            }
        }

        if ($error == 0) {
            cmsg::add('success', clang::__("Role") . " \"" . $id . "\" " . clang::__("Successfully Deleted"));
        } else {
            //proses gagal
            cmsg::add('error', $error_message);
        }
        curl::redirect('users');
    }

}