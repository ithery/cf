<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Menus_Controller extends CController {

    public function index($parent_id = "") {
        $app = CApp::instance();
        $org = $app->org();
        $db = CDatabase::instance();
        $org_id = "";
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $parent_parent_id = "0";
        $parent_name = "";
        if (strlen($parent_id) > 0) {
            if (strlen($parent_id) == 0)
                $parent_id = "0";
            $q = "select name from menu where menu_id=" . $db->escape($parent_id) . "";
            $r = $db->query($q);
            if ($r->count() > 0) {
                $parent_name = $r[0]->name;
            } else {
                $parent_id = "0";
            }

            if ($parent_id != "0") {
                $q = "select parent_id from menu where menu_id=" . $db->escape($parent_id) . "";
                $r = $db->query($q);
                if ($r->count() > 0) {
                    $parent_parent_id = $r[0]->parent_id;
                }
            }
        }
        if (strlen($parent_id) == 0)
            $parent_id = "0";


        $q = '';
        $q .= "select " .
                " u.menu_id" .
                " ,u.name" .
                " ,ifnull(u.parent_id,0) as parent_id" .
                " ,u.caption" .
                " ,u.controller" .
                " ,u.method" .
                " ,u.seqno" .
                " ,u.status" .
                " from menu u" .
                " where " .
                "  u.app_id='1' " .
                "";
        if ($parent_id != "0") {
            $q .= " and u.parent_id='" . ($parent_id) . "'";
        } else {
            $q .= " and u.parent_id is null ";
        }
        $q .= " order by u.seqno asc";
        $r = $db->query($q);

        $app->title("Menu" . " " . $parent_name . "");
        $table = CTable::factory('menus_table');
        $table->add_column('name')->set_label('Menu');
        $table->add_column('caption')->set_label('Caption')->set_editable(false);
        $table->add_column('controller')->set_label('Controller')->set_editable(false);
        $table->add_column('method')->set_label('Method')->set_editable(false);
        $table->set_data_from_query($q)->set_key('menu_id');
        $table->set_apply_data_table(false);
        $table->cell_callback_func(array("Menus_Controller", "cell_callback"));
        //$table->add_row_action('edit')->set_label('Edit')->set_icon('pencil');
        //$table->add_row_action('delete')->set_icon('trash');
        $table->set_title('Menus');

        $actions = CActionList::factory("reminder_action");

        $actadd = CAction::factory('add');
        $actadd->set_label("Add Menus")->set_icon("plus")->set_link(curl::base() . "menus/add/" . $parent_id);
        $actions->add($actadd)->set_style("icon-segment");


        $actedit = $table->add_row_action('edit');
        $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . "menus/edit/{parent_id}/{param1}");
        $actedit = $table->add_row_action('delete');
        $actedit->set_label("")->set_icon("trash")->set_link(curl::base() . "menus/delete/{param1}")->set_confirm(true);

        $rowfluid = CRowFluid::factory('role-row-fluid');
        $rowfluid->add($actions);

        //$table->set_ajax(true);
        //$table->set_editable(true);
        $app->add($rowfluid);
        $app->add("<br />");
        $app->add($table);

        $actback = CAction::factory('back');
        $actback->set_label(" Back")->set_icon("repeat")->set_link(curl::base() . "menus/index/" . $parent_parent_id);

        $actions_back = CActionList::factory("reminder_action");
        $actions_back->add($actback)->set_style("icon-segment");
        $rowfluid_back = CRowFluid::factory('menu-back-row-fluid');
        $rowfluid_back->add($actions_back);
        $app->add($rowfluid_back);
        //$html.= "<br />".ui_button::back(curl::base()."menu/index/".$parent_parent_id);


        echo $app->render();
    }

    public function add($parent_id) {
        $this->edit($parent_id);
    }

    public function edit($parent_id = "", $id = "") {
        $app = CApp::instance();
        $app->title("Edit Menu");
        $session = Session::instance();
        $post = $this->input->post();
        $db = CDatabase::instance();
        $is_add = 0;

        $user = $app->user();
        $org = $app->org();
        $org_id = "";

        if ($org != null) {
            $org_id = $org->org_id;
        }
        $action = $is_add == 0 ? "edit" : "add";
        $name = "";
        $caption = "";
        $controller = "";
        $method = "";
        $seqno = "";
        $have_add = "";
        $have_edit = "";
        $have_delete = "";
        $have_confirm = "";
        $have_download = "";
        $status = "";
        $icon = "";
        if ($parent_id == "") {
            if (isset($post["parent_id"]))
                $parent_id = $post["parent_id"];
        }
        if ($parent_id == "") {
            $parent_id = "0";
        }
        if (strlen($id) == 0) {
            if ($post != null && isset($post["id"]))
                $id = $post["id"];
        }
        if (strlen($parent_id) == 0) {
            if ($post != null && isset($post["parent_id"]))
                $parent_id = $post["parent_id"];
        }
        if (strlen($id) == 0) {
            $is_add = 1;
        }
        $parent_name = "";
        if ($parent_id != 0) {
            $q = "select name from menu where menu_id='" . $parent_id . "'";
            $r = $db->query($q);
            if ($r->count() > 0) {
                $parent_name = $r[0]->name;
            }
        }
        if ($post != null) {

            $error = 0;
            $error_message = "";
            try {

                $name = $post["name"];
                $caption = $post["caption"];
                $controller = $post["controller"];
                $method = $post["method"];
                $seqno = $post["seqno"];
                $status = $post["status"];
                $icon = $post["icon"];
                $have_add = '0';
                $have_edit = '0';
                $have_delete = '0';
                $have_confirm = '0';
                $have_download = '0';
                if (isset($post["have_add"]))
                    $have_add = $post["have_add"];
                if (isset($post["have_edit"]))
                    $have_edit = $post["have_edit"];
                if (isset($post["have_delete"]))
                    $have_delete = $post["have_delete"];
                if (isset($post["have_download"]))
                    $have_void = $post["have_download"];
                if (isset($post["have_confirm"]))
                    $have_print = $post["have_confirm"];
                //checking
                if ($error == 0) {
                    if (strlen($name) == 0) {
                        $error_message = "Menu name is required !";
                        $error++;
                    }
                }


                if ($error == 0) {
                    $qcheck = "select * from menu where name='" . $name . "'";
                    if ($is_add == 0)
                        $qcheck .= " and name<>'" . $name . "'";
                    $rcheck = $db->query($qcheck);
                    if ($rcheck->count() > 0) {
                        $error_message = "Menu name is already exist, please try another name !";
                        $error++;
                    }
                }


                if ($error == 0) {
                    $data = array(
                        "name" => $name,
                        "caption" => $caption,
                        "controller" => $controller,
                        "method" => $method,
                        "seqno" => $seqno,
                        "name" => $name,
                        "icon" => $icon,
                        "have_add" => $have_add,
                        "have_edit" => $have_edit,
                        "have_delete" => $have_delete,
                        "have_confirm" => $have_confirm,
                        "have_download" => $have_download,
                        "status" => $status,
                    );
                    if ($parent_id != "0" && $parent_id != "") {
                        $data = array_merge($data, array("parent_id" => $parent_id));
                    }
                    if (strlen($id) == 0) {
                        $data = array_merge($data, array(
                            "created" => date("Y-m-d H:i:s"),
                            "app_id" => "1",
                        ));

                        $db->insert("menu", $data);
                    } else {
                        $data = array_merge($data, array(
                            "updated" => date("Y-m-d H:i:s"),
                        ));

                        $db->update("menu", $data, array("menu_id" => $id));
                    }
                }
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = "Error, call administrator..." . $e->getMessage();
                ;
            }
            if ($error == 0) {
                if ($id > 0) {

                    cmsg::add("success", "Menu \"" . $name . "\" Successfully Modified !");
                } else {
                    cmsg::add("success", "Menu \"" . $name . "\" Successfully Added !");
                }
                curl::redirect("menus/index/" . $parent_id);
            } else {
                cmsg::add("error", $error_message);
            }
        } else if (strlen($id) > 0) {
            $q = "select " .
                    " name" .
                    " ,caption" .
                    " ,seqno" .
                    " ,controller" .
                    " ,icon" .
                    " ,method" .
                    " ,have_add" .
                    " ,have_edit" .
                    " ,have_delete" .
                    " ,have_confirm" .
                    " ,have_download" .
                    " ,parent_id" .
                    " ,status" .
                    " " .
                    " from menu where menu_id = '" . $id . "';";
            $result = $db->query($q);
            if ($result->count() > 0) {
                $row = $result[0];
                $name = $row->name;
                $icon = $row->icon;
                $caption = $row->caption;
                $controller = $row->controller;
                $method = $row->method;
                $seqno = $row->seqno;
                $have_add = $row->have_add;
                $have_edit = $row->have_edit;
                $have_confirm = $row->have_confirm;
                $have_delete = $row->have_delete;
                $have_download = $row->have_download;
                $status = $row->status;
            }
        }

        $html = '';


        $widget = CWidget::factory('menu-widget');

        $actions = CActionList::factory('act_menus');
        $act_next = CAction::factory('submit')->set_label('Submit')->set_submit(true);

        $actions->add($act_next);
        $actions->set_style('form-action');
        $form = CForm::factory('roles');
        $form->add_field('parent-field')->set_label('Parent')->add_control('name', 'label')->add_validation('required')->set_value(($parent_name == "" ? "-" : $parent_name));
        $form->add_field('name-field')->set_label('Name')->add_control('name', 'text')->add_validation('required')->set_value($name);
        $form->add_field('caption-field')->set_label('Caption')->add_control('caption', 'text')->add_validation(null)->set_value($caption);
        $form->add_field('controller-field')->set_label('Controller')->add_control('controller', 'text')->add_validation(null)->set_value($controller);
        $form->add_field('method-field')->set_label('Method')->add_control('method', 'text')->add_validation(null)->set_value($method);
        $form->add_field('seqno-field')->set_label('Seqno')->add_control('seqno', 'text')->add_validation('number')->set_value($seqno);
        $form->add_field('icon-field')->set_label('Icon')->add_control('icon', 'text')->add_validation(null)->set_value($icon);
        $list = array("1" => "ENABLED", "0" => "DISABLED");
        $form->add_field('status-field')->set_label('Status')->add_control('status', 'select')->add_validation(null)->set_value($status)->set_list($list);
        $form->add_control('id', 'hidden')->add_validation(null)->set_value($id);
        $form->add($actions);
        $widget->add($form);


        $app->add($widget);


        echo $app->render();
    }

    public function delete($id = "") {
        if (strlen($id) == 0) {
            curl::redirect(Router::$controller);
        }
        $session = Session::instance();
        $db = CDatabase::instance();
        $q = '';
        $error = 0;
        if ($error == 0) {
            try {
                $db->delete("roles", array("role_id" => $id));
            } catch (Kohana_Exception $e) {
                $error++;
                $error_message = "Fail on delete, please call the administrator...";
            }
        }

        if ($error == 0) {
            cmsg::add('success', "Role \"" . $id . "\" Successfully Deleted !");
        } else {
            //proses gagal
            cmsg::add('error', $error_message);
        }
        curl::redirect(Router::$controller);
    }

    public static function cell_callback($table, $col, $row, $text) {
        if ($col == "name") {
            return '<a href="' . curl::base() . 'menus/index/' . ($row["menu_id"]) . '" title="' . ($row["name"]) . '">' . $text . '</a>';
        }
        return $text;
    }

}