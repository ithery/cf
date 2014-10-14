<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Users_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Users"));


        $cdb = CJDB::instance();
        $org_list = $cdb->get_list('org', 'org_id', 'name');
        foreach ($org_list as $k => $v) {
            $org_id = $k;
            break;
        }
        $get = $_GET;
        if (isset($get["org_id"])) {
            $org_id = $get["org_id"];
        }
        $widget = $app->add_widget()->set_icon('filter')->set_title('Filter');
        $form = $widget->add_form()->set_method('get');
        $form->add_field()->set_label(clang::__('Organization'))->add_control('org_id', 'select')->set_value($org_id)->set_list($org_list)->set_submit_onchange(true);

        $actions = $app->add_div()->add_class('row-fluid')->add_div()->add_class('span12')->add_action_list();
        $actions->add_action()->set_label(" " . clang::__("Add") . " " . clang::__("Users"))->set_icon("plus")->set_link(curl::base() . "admin/users/add/" . $org_id);

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
                " from users u inner join roles as r on u.role_id=r.role_id where u.status>0 ";
        $q.= ' and u.org_id=' . $db->escape($org_id);
        $table->set_data_from_query($q)->set_key('user_id');
        //$table->add_row_action('edit')->set_label('Edit')->set_icon('pencil');
        //$table->add_row_action('delete')->set_icon('trash');
        $table->set_action_style("btn-dropdown");
        $table->set_title('Users');

        $actedit = $table->add_row_action('edit');
        $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . "admin/users/edit/" . $org_id . "/{param1}")->set_label(clang::__('Edit User'));
        $actedit = $table->add_row_action('delete');
        $actedit->set_label("")->set_icon("trash")->set_link(curl::base() . "admin/users/delete/" . $org_id . "/{param1}")->set_confirm(true)->set_label(clang::__('Delete User'));

        echo $app->render();
    }

    public function add($org_id) {
        $this->edit($org_id);
    }

    public function edit($org_id, $id = "") {
        $app = CApp::instance();
        $title = clang::__("Edit") . " " . clang::__("Users");
        $icon = "pencil";
        if (strlen($title) == 0) {
            $title = clang::__("Add") . " " . clang::__("Users");
            $icon = "plus";
        }
        $app->title($title);
        $app->add_breadcrumb(clang::__("Users"), curl::base() . "admin/users?org_id=" . $org_id);
        $session = Session::instance();
        $post = $this->input->post();
        $db = CDatabase::instance();
        $is_add = 0;
        if (strlen($id) == 0) {
            $is_add = 1;
        }

        $user = $app->user();
        $action = $is_add == 0 ? "edit" : "add";
        $username = "";
        $description = "";
        $role_id = "";
		$store_id = "";
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
                        $error_message = "Username is required !";
                        $error++;
                    }
                }

                if ($error == 0) {
                    if ($is_add == 1) {
                        if (strlen($password) == 0) {
                            $error_message = "Password is required !";
                            $error++;
                        }
                    }
                }



                if ($error == 0) {
                    $qcheck = "select * from users where status>0 and username='" . $username . "'";
                    if ($is_add == 0)
                        $qcheck .= " and username<>'" . $username . "'";
                    $rcheck = $db->query($qcheck);
                    if ($rcheck->count() > 0) {
                        $error_message = "Username is already exist, please try another name !";
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
                            "createdby" => 'administrator',
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => 'administrator',
                        ));
                        $r = $db->insert("users", $data);
                        $user_id = $r->insert_id();
                    } else {
                        $data = array_merge($data, array(
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => 'administrator'
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
                                "createdby" => 'administrator',
                            );
                            $db->insert("users_store", $data);
                        }
                    }
                }
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = "Error, call administrator..." . $e->getMessage();
                ;
            }
            if ($error == 0) {
                if ($id > 0) {
                    cmsg::add("flash_success", "User \"" . $username . "\" Successfully Modified !");
                } else {
                    cmsg::add("success", "User \"" . $username . "\" Successfully Added !");
                }
                curl::redirect("admin/users?org_id=" . $org_id);
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
                    " from users v where v.user_id = '" . $id . "';";
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
        $role_list = cdbutils::get_list("select role_id as k,concat(name) as v from roles where org_id=" . $db->escape($org_id) . "order by role_id asc,name asc;");

        $form = $widget->add_form();
        $form->add_field('role-field')->set_label('Role')->add_control('role_id', 'select')->add_validation(null)->set_value($role_id)->set_list($role_list);
        $form->add_field('username-field')->set_label('Username')->add_control('username', 'text')->add_validation('required')->set_value($username);
        $form->add_field('password-field')->set_label('Password')->add_control('password', 'password')->add_validation(null)->set_value('');
        $form->add_field('description-field')->set_label('Description')->add_control('description', 'textarea')->add_validation(null)->set_value($description);
        if ($app->have_store()) {
            $form->add_field()->set_label('Store')->add_control('store_id', 'checkbox-list')->add_validation(null)->set_value($store_id)->set_list($app->store_list());
        }
        $form->add_control('id', 'hidden')->add_validation(null)->set_value($id);
        $actions = $form->add_action_list();
        $actions->set_style('form-action');
        $act = $actions->add_action();
        $act->set_label('Submit')->set_submit(true);



        echo $app->render();
    }

    public function delete($org_id, $id = "") {
        if (strlen($id) == 0) {
            curl::redirect(Router::$controller);
        }
        $app = CApp::instance();

        $db = CDatabase::instance();
        $q = '';
        $error = 0;

        $user = cuser::get($id);

        if ($error == 0) {
            try {
                $db->update("users", array("status" => 0, "updated" => date("Y-m-d H:i:s"), "updatedby" => 'administrator'), array("user_id" => $id));
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = "Fail on delete, please call the administrator...";
            }
        }

        if ($error == 0) {
            cmsg::add('success', "Users \"" . $user->username . "\" Successfully Deleted !");
        } else {
            //proses gagal
            cmsg::add('error', $error_message);
        }
        curl::redirect('admin/users?org_id=' . $org_id);
    }

}