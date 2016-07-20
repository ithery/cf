<?php

/**
 * Description of menu
 *
 * @author Ecko Santoso
 * @since 29 Sep 15
 */
class Controller_Cms_Setting_Menu extends CController {

    CONST __CONTROLLER = "cms/setting/menu/";

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        curl::redirect(self::__CONTROLLER.'add');
    }

    private function update_recursive($data, $parent_id =null) {
        $db = CDatabase::instance();
        $app = CApp::instance();

        $user = $app->user();
        $org_id = null;
        $priority = 0;
        
        foreach ($data as $d) {
            $id = $d['id'];

            $data_updated['parent_id'] = $parent_id;
            $data_updated['priority'] = $priority;

            $db->update('cms_menu', $data_updated, array('cms_menu_id' => $id));
            $children = array();
            if (isset($d['children'])) {
                $this->update_recursive($d['children'], $id);
            }
            $priority++;
        }
    }
    
    public function view_menu($menu_group_id) {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        /**
        * org_id set null karena data yg didatabase terlanjur null untuk org_id (efek dump dari local)
        * maka di jika org_id tidak null, otomatis CTreeDB akan mengambil data dengan org_id tertentu
        */
        // $org = $app->org();
        // if($org!=null) {
            // $org_id = $org->org_id;
        // }
        
        $request = array_merge($_GET,$_POST);
        $tree = CTreeDB::factory('cms_menu');
        $tree->set_org_id($org_id);
        $tree->add_filter('cms_terms_id', $menu_group_id);
        if(isset($request['data_order'])) {
            $error = 0;
            $data = $request['data_order'];
            $data = cjson::decode($data);

            try {
                $db->begin();
                if (!is_array($data)) {
                    throw new Exception('Invalid Data');
                }
                $this->update_recursive($data, null);
                $q = "select * from cms_menu where parent_id is null ";
                if (strlen($menu_group_id) > 0) {
                    $q.=" and cms_terms_id=" . $db->escape($menu_group_id) . " ";
                }
                if (strlen($org_id) > 0) {
                    $q.=" and org_id=" . $db->escape($org_id) . " ";
                }

                $q .= " ORDER BY priority";
                
                $r = $db->query($q);
                $left = 1;
                $tree->set_have_priority(true);
                foreach ($r as $row) {
                    $tree->rebuild_tree($row->cms_menu_id, $left);
                    $left = cdbutils::get_value('select rgt from cms_menu where cms_menu_id=' . $db->escape($row->cms_menu_id)) + 1;
                }
            } catch (Exception $e) {
                $error++;
                $error_message = clang::__("system_update_fail") . " " . $e->getMessage();
            }
            if ($error == 0) {
                $db->commit();
                cmsg::add('success', clang::__("Menu Order") . clang::__(" ") . clang::__("Successfully Modified") . " !");
//                clog::activity($user->user_id, 'edit', clang::__("Role Order") . clang::__(" ") . clang::__("Successfully Modified") . " !");
//                curl::redirect('menu');
            } else {
                //proses gagal
                $db->rollback();
                cmsg::add('error', $error_message);
            }
        }
        $widget = $app->add_widget();
        $widget->clear_both();
        $nestable = $widget->add_nestable();
        $nestable->display_callback_func(array('Controller_Cms_Setting_Menu','display_menu_detail'),__FILE__);
        
        $nestable->set_data_from_treedb($tree)->set_id_key('cms_menu_id')->set_value_key('name')->set_input('data_order');
        $widget->add_control('data_order', 'hidden')->set_value('');
        
        $nestable->set_applyjs(true);
        $nestable->set_action_style('btn-dropdown');
        
        if (cnav::have_permission('edit_menu_item')) {
            $actedit = $nestable->add_row_action('edit');
            $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . self::__CONTROLLER."edit/{cms_menu_id}")->set_label(" " . clang::__("Edit"));
        }

        if (cnav::have_permission('delete_menu_item')) {
            $actedit = $nestable->add_row_action('delete');
            //$nestable->set_action_style('btn-group');
            $actedit->set_label("")->set_icon("trash")->set_link(curl::base() . self::__CONTROLLER ."delete/{cms_menu_id}")->set_confirm(true)->set_label(" " . clang::__("Delete"));
        }
        
//        $action = $widget->add_header_action();
//        $action->set_label('Save')->add_listener('click')->add_handler('reload')->set_target('div-menu-view')->set_url(curl::base(). self::__CONTROLLER.'view_menu/'.$menu_group_id)->add_param_input(array('data_order'));
        
        echo $app->render();
    }
    
    public function display_menu_detail($object, $data, $value) {
        $menu_name = carr::get($data, 'name');
        $menu_type = ucfirst(carr::get($data, 'menu_item_object'));
        
        $text = $menu_name;
        if ( strlen($menu_type) > 0) {
            $text .= ' - ('.$menu_type.')';
        }
//        return cdbg::var_dump($data,true);
        return $text;
    }
    
    public function parent_menu($menu_group_id) {
        $app = CApp::instance();
        $tree = CTreeDB::factory('cms_menu');
        $tree->add_filter('cms_terms_id', $menu_group_id);
        $parent_list[''] = '[NONE]';
        $child_list = $tree->get_children_list(null, "&nbsp;&nbsp;");
        $parent_list = $parent_list + $child_list;
        
        $app->add_field()->set_label(clang::__('Parent'))
                ->add_control('parent_id', 'select')
                ->set_list($parent_list);
        
        echo $app->render();
    }
    
    public function menu_type($menu_type = '', $menu_item_value = '') {
        $app = CApp::instance();
        $org_id = CF::org_id();
        $role = $app->role();
        $is_administrator = false;
        if (cobj::get($role, 'parent_id') == NULL) {
            $is_administrator = true;
        }
        
        $category_list = ccms::get_category_list(array('category_type'=>'ALL'));
        $page_list = ccms::page_select($org_id);
        
        if (strlen($menu_type) > 0) {
            $control = $app->add_field()->set_label(clang::__(ucfirst($menu_type)).' <red>*</red>');
        }
        switch ($menu_type) {
            case 'page':
                $field_control = $control->add_control("menu_item_value", "select")
                    ->set_value($menu_item_value)
                    ->set_list($page_list);
                break;
            case 'category':
                $field_control = $control->add_control("menu_item_value", "select")
                    ->set_value($menu_item_value)
                    ->set_list($category_list);
                break;
            case 'custom':
                if (strlen(base64_decode($menu_item_value, true)) > 1) {
                    $menu_item_value = base64_decode($menu_item_value);
                }
                else {
                    $menu_item_value = "";
                }
                
                $field_control = $control->add_control("menu_item_value", "text")
                    ->set_value($menu_item_value);
                break;
            default :
                //$control->add_control("menu_item_value", "text");
                break;
        }
        if (strlen($menu_type) > 0) {
            if (cnav::have_permission('edit_menu_item')) {
                if ($is_administrator == false) {
                    $field_control->set_readonly(true);
                }
            }
        }
        
        echo $app->render();
    }

    public function add() {
        $this->edit();
    }
    
    public function edit($id = '') {
        $app = CApp::instance();
        $user = $app->user();
        $role = $app->role();
        $is_administrator = false;
        if (cobj::get($role, 'parent_id') == NULL) {
            $is_administrator = true;
        }
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        
        $app->title(clang::__("Menu List"));
        
        $is_add = 0;
        if (strlen($id) == 0) {
            $is_add = 1;
        }
        
        $post = $_POST;
        
        // Declare default value
        $cms_terms_id = '';
        $menu_name = '';
        $menu_type = '';
        $menu_classes = '';
        $cms_post_id = '';
        $menu_item_value = '';

        // Load menu group list
        $menu_group = ccms::menu_group();
        
        // GET VALUE IF EDIT
        if (strlen($id) > 0) {
            $get_menu = cdbutils::get_row("SELECT * FROM cms_menu WHERE cms_menu_id = ".$db->escape($id)." AND status > 0");
//            $app->add($get_menu);
            if ($get_menu != NULL) {
                $menu_name = $get_menu->name;
                $cms_terms_id = $get_menu->cms_terms_id;
                $menu_type = $get_menu->menu_item_object;
//                $menu_classes = $get_menu->menu_item_classes;
                
                $menu_item_value = $get_menu->menu_item_object_id;
                if ($menu_type == 'custom') {
                    $menu_item_value = base64_encode($get_menu->menu_item_url);
                }
//                if ($menu_type != "category") {
                    $cms_post_id = $get_menu->menu_item_object_id;
//                }
//                $get_terms = cdbutils::get_row("SELECT * FROM cms_terms WHERE cms_terms_id = ".$db->escape($cms_terms_id));
//                $app->add($get_terms);
//                cdbg::var_dump($get_menu);
//                cdbg::var_dump($cms_post_id);
//                die();
            }
        }
        
        // PROCESS
        if ($post != NULL) {
            $err_code = 0;
            $err_message = '';
            
            $cms_terms_id = $post["menu_group"];
            $parent_id = $post["parent_id"];
            $menu_name = $post["menu_name"];
            $menu_type = $post["menu_type"];
            if (isset($post['menu_item_value'])) {
                $menu_item_value = $post["menu_item_value"];
            }
            $menu_classes = carr::get($post, 'menu_classes');
            //checking
            if ($err_code == 0) {
                if (strlen($menu_name) == 0) {
                    $err_message = clang::__("Menu name is required !");
                    $err_code++;
                }
            }
            if ($err_code == 0) {
                $qcheck = "select * from cms_menu where name=" . $db->escape($menu_name) . " and status>0 ";
                if (strlen($org_id) > 0) {
                    $qcheck.="and org_id=" . $db->escape($org_id);
                }

                if ($is_add == 0)
                    $qcheck .= " and name<>" . $db->escape($menu_name) . "";
                $rcheck = $db->query($qcheck);
                if ($rcheck->count() > 0) {
                    $err_message = clang::__("Menu name is already exist, please try another name !");
                    $err_code++;
                }
            }
            
            if (strlen($menu_item_value) == 0) {
                $err_code++;
                $err_message = clang::__("Menu from type is required");
            }
            
            if (strlen($parent_id) == 0) {
                $parent_id = null;
            }
            
            if ($err_code == 0) {
                // Declare Tree and set filter by menu_group
                $tree = CTreeDB::factory('cms_menu');
                $tree->add_filter("cms_terms_id", $cms_terms_id);
                
                try {
                    if (strlen($id) > 0) {
//                        cdbg::var_dump($post);
//                        cdbg::var_dump($cms_post_id);
                        // EDIT MENU ITEM
                        $default = array(
                            'updated' => date('Y-m-d H:i:s'),
                            'updatedby' => cobj::get($user, 'username')
                        );
                        
                        // 1ST => update cms_post
                        $post_data = array(
                            "org_id" => $org_id,
                            "post_title" => $menu_name,
                            "post_status" => "publish",
                            "post_type" => "nav_menu_item"
                        );
//                        $update_post = $db->update("cms_post", array_merge($post_data, $default), array("cms_post_id"=>$cms_post_id));
                        
                        // 2ND => Update cms_menu
                        $data_menu = array(
                            "org_id" => $org_id,
                            "parent_id" => $parent_id,
                            "name" => $menu_name,
                            "menu_item_object" => $menu_type,
                            "cms_terms_id" => $cms_terms_id,
//                            "menu_item_classes" => $menu_classes,
                            "menu_item_url" => ""
                        );

                        if ($menu_type == 'custom') {
                            $data_menu['menu_item_type'] = "custom";
                            $data_menu['menu_item_object_id'] = $cms_post_id;
                            $data_menu['menu_item_url'] = $menu_item_value;
                        }
                        if ($menu_type == 'page') {
                            $data_menu['menu_item_type'] = "post_type";
                            $data_menu['menu_item_object_id'] = $menu_item_value;
                        }
                        if ($menu_type == 'category') {
                            $data_menu['menu_item_type'] = "taxonomy";
                            $data_menu['menu_item_object_id'] = $menu_item_value;
                        }
                        $data = array_merge($data_menu, $default);
                        $tree->update($id, $data, $parent_id);
                    }
                    else {
                        // ADD NEW MENU ITEM
                        $default = array(
                            'created' => date('Y-m-d H:i:s'),
                            'createdby' => cobj::get($user, 'username')
                        );

                        // 1ST => insert into cms_post
                        $post_data = array(
                            "org_id" => $org_id,
                            "post_title" => $menu_name,
                            "post_status" => "publish",
                            "post_type" => "nav_menu_item"
                        );
                        $insert_post = $db->insert("cms_post", array_merge($post_data, $default));
                        $cms_post_id = $insert_post->insert_id();

                        // 2ND => Insert into cms_menu
                        $data_menu = array(
                            "org_id" => $org_id,
                            "parent_id" => $parent_id,
                            "name" => $menu_name,
                            "menu_item_object" => $menu_type,
                            "cms_terms_id" => $cms_terms_id,
//                            "menu_item_classes" => $menu_classes,
                            "menu_item_url" => ""
                        );

                        if ($menu_type == 'custom') {
                            $data_menu['menu_item_type'] = "custom";
                            $data_menu['menu_item_object_id'] = $cms_post_id;
                            $data_menu['menu_item_url'] = $menu_item_value;
                        }
                        if ($menu_type == 'page') {
                            $data_menu['menu_item_type'] = "post_type";
                            $data_menu['menu_item_object_id'] = $menu_item_value;
                        }
                        if ($menu_type == 'category') {
                            $data_menu['menu_item_type'] = "taxonomy";
                            $data_menu['menu_item_object_id'] = $menu_item_value;
                        }
                        $data = array_merge($data_menu, $default);
                        $tree->insert($data, $parent_id);
                    }
                }
                catch (Exception $e) {
                    $err_code++;
                    $err_message = clang::__("Error, call administrator") . "..." . $e->getMessage();
                }
            }
            
            if ($err_code == 0) {
                if (strlen($id) > 0) {
                    cmsg::add("success", clang::__("Menu") . " [" . $menu_name . "] " . clang::__("Successfully Modified !"));
                } else {
                    cmsg::add("success", clang::__("Menu") . " [" . $menu_name . "] " . clang::__("Successfully Added !"));
                }
                curl::redirect(self::__CONTROLLER.'add');
            } else {
                cmsg::add("error", $err_message);
            }
        }
        
        // BUILD VIEW
        // Widget filter menu group
        $widget = $app->add_widget()->set_title(clang::__("Menu"));
        $widget->add_class("clearfix");

        $div_l = $widget->add_div()->add_class('span4');
        $div_r = $widget->add_div('div-menu-view')->add_class('span6 offset1');

        $form = $div_l->add_form();

        $menu_type_list = array(
            '' => clang::__("Please Select"),
            'page' => clang::__("Page"),
            'category' => clang::__("Category"),
            'custom' => clang::__("Link")
                );
        $menu_icon_list = array(
            '' => "- None -",
            'glyphicon-home' => "Home",
            'glyphicon-book' => "Newspaper",
            'glyphicon-tags' => "Tag"
        );
        $select_menu = $form->add_field()->set_label(clang::__("Select") . ' ' . clang::__("Menu Group"))
                ->add_control('menu_group', 'select')
                ->set_value($cms_terms_id)
                ->set_list($menu_group);
        $select_menu->add_listener("change")
                ->add_handler("reload")
                ->set_target("div-menu-view")
                ->set_url(curl::base() . self::__CONTROLLER . 'view_menu/{menu_group}');
        
        $div_l->add_listener('ready')->add_handler('reload')->set_target('div-menu-view')
                ->set_url(curl::base() . self::__CONTROLLER . 'view_menu/{menu_group}');
        
        $form->add_control('parent_id', 'hidden');
        
        $field_menu_name = $form->add_field()->set_label('<red>*</red> '.clang::__("Name"))
                ->add_control('menu_name', 'text')
                ->set_value($menu_name)->add_class('large');
        
        $select_menu_type = $form->add_field()->set_label(clang::__("Add Menu From").' <red>*</red>')
                ->add_control('menu_type', 'select')
                ->set_value($menu_type)
                ->set_list($menu_type_list);
        $select_menu_type
                ->add_listener("change")
                ->add_handler("reload")
                ->set_target("div-menu-type")
                ->set_url(curl::base() . self::__CONTROLLER.'menu_type/{menu_type}/'.$menu_item_value);
        $div_l->add_listener('ready')->add_handler('reload')->set_target('div-menu-type')
                ->set_url(curl::base() . self::__CONTROLLER.'menu_type/{menu_type}/'.$menu_item_value);
        $div_menu_type = $form->add_div('div-menu-type');
        
        $field_menu_icon = $form->add_field()->set_label(clang::__("Icon"))
                ->add_control('menu_classes', 'select')
                ->set_value($menu_classes)
                ->set_list($menu_icon_list)
                ->add_class('select-icon');
        
        if (strlen($id) > 0) {
            if (cnav::have_permission('edit_menu_item')) {
                $form->add_action_list()->add_action()->set_label(clang::__("Submit"))->set_confirm(true)->set_submit(true);
                if ($is_administrator == false) {
                    $select_menu->set_readonly(true);
                    $select_menu_type->set_readonly(true);
                    $field_menu_icon->set_readonly(true);
                }
            }
        }
        else {
            if (cnav::have_permission('add_menu_item')) {
                $form->add_action_list()->add_action()->set_label(clang::__("Submit"))->set_confirm(true)->set_submit(true);
            }
        }
        
        echo $app->render();
    }
    
    public function delete($id = "") {
        if (strlen($id) == 0) {
            curl::redirect(self::__CONTROLLER);
        }
        $app = CApp::instance();
        $db = CDatabase::instance();
        $error = 0;
        
        $get_menu = cdbutils::get_row("SELECT * FROM cms_menu WHERE cms_menu_id = ".$db->escape($id));
        $tree = CTreeDB::factory('cms_menu');
        $tree->add_filter("cms_terms_id", cobj::get($get_menu, 'cms_terms_id'));
        $tree->add_filter("cms_menu_id", $id);
        $tree->set_delete_child(true);
        
        if ($error == 0) {
            try {
                $tree->delete($id);
                //$db->update("roles",array("status"=>"0","updated"=>date("Y-m-d H:i:s"),"updatedby"=>$app->user()->username),array("role_id" => $id));
                //$db->delete("roles", array("role_id" => $id));
            } catch (Exception $e) {
                $error++;
                $error_message = clang::__("Fail on delete, please call the administrator..."). $e->getMessage();
            }
        }
        if ($error == 0) {
            cmsg::add('success', clang::__("Menu") . "[" . $get_menu->name . "]" . clang::__("Successfully Deleted"));
        } else {
            //proses gagal
            cmsg::add('error', $error_message);
        }
        curl::redirect(self::__CONTROLLER);
    }
    
    // MENU GROUP
    public function group() {
        $app = CApp::instance();
        
        // MENU GROUP
        // Menu Group Data get from helper
        $menu_group_items = ccms::menu_group_list();
        
        $div_menu_group = $app->add_div()->add_class('row-fluid');
        $menu_group_l = $div_menu_group->add_div()->add_class("span7");
        $menu_group_r = $div_menu_group->add_div('div-add-menu-group')->add_class("span5");
        $menu_group_r->add_listener('ready')->add_handler('reload')->set_target('div-add-menu-group')->set_url(curl::base() . self::__CONTROLLER. 'add_menu_group');
        
        
        // BUILD TABLE
        $div_table = $menu_group_l->add_div('div-table-container');
        $table = $div_table->add_table('menu_group_table')->set_quick_search(FALSE);
        $table->add_column('name')->set_label(clang::__("Name"));
        $table->add_column('slug')->set_label(clang::__("Menu Name"));
        $table->add_column('created')->set_label(clang::__("Created"))->add_transform('format_datetime');
        $table->add_column('createdby')->set_label(clang::__("Created By"));
        $table->set_data_from_array($menu_group_items)->set_key('cms_terms_id');
        $table->set_title(clang::__("Menu Group"));

//        $btn_add_menu_group = $menu_group_l->add_action()->set_label(clang::__("Add Menu Group"))->add_class('btn-info');
//        $btn_add_menu_group->add_listener('click')->add_handler('reload')->set_url(curl::base() . self::__CONTROLLER. 'add_menu_group')->set_target('div-add-menu-group');
        // Table action
        if (cnav::have_permission('edit_menu_group')) {
            $actedit = $table->add_row_action('edit')->set_label(clang::__("Edit"))->set_icon("pencil");
            $actedit->add_listener("click")
                    ->add_handler("reload")
                    ->set_target('div-add-menu-group')
                    ->set_url(curl::base() . self::__CONTROLLER. 'add_menu_group/{cms_terms_id}');
        }
//        $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . self::__CONTROLLER."group/{cms_terms_id}")->set_label(clang::__('Edit'));
        if (cnav::have_permission('delete_menu_group')) {
            $actdelete = $table->add_row_action('delete');
            $actdelete->set_label("")->set_icon("trash")->set_link(curl::base() . self::__CONTROLLER. "menu_group_delete/{cms_terms_id}")->set_confirm(true)->set_label(clang::__('Delete'));
        }
        $table->set_action_style("btn-dropdown");
        
        echo $app->render();
    }
    
    public function add_menu_group($id = '') {
        $app = CApp::instance();
        $db = CDatabase::instance();
        
        $name = '';
        $slug = '';
        
        if (strlen($id) > 0) {
            $get_term = cdbutils::get_row("SELECT * FROM cms_terms WHERE cms_terms_id =".$db->escape($id)." AND status > 0");
            if ($get_term != NULL) {
                $name = $get_term->name;
                $slug = $get_term->slug;
            }
        }
        
        $form = $app->add_form();
        $form->set_action(curl::base() . self::__CONTROLLER.'save_menu_group/'.$id);
        $widget = $form->add_widget();
        $widget->set_icon("plus");
        $widget->set_title(clang::__("Add Menu Group"));
        
        $widget->add_field()->set_label('* ' . clang::__('Name'))->add_control('name', 'text')->set_value($name);
        $input_menu_name = $widget->add_field()->set_label('* ' . clang::__('Menu Name'))->add_control('slug', 'text')->set_value($slug);
        if (strlen($id) > 0) {
            $input_menu_name->set_readonly(true);
        }

        $widget->add_action_list()->add_action()->set_label(clang::__("Submit"))->set_confirm(true)->set_submit(true);
        
        $js = "jQuery('#name').keyup(function(){
                    var Title = jQuery(this).val();
                    jQuery('#slug').val(convertToSlug(Title));
                });
                function convertToSlug(Text)
                {
                    return Text
                        .toLowerCase()
                        .replace(/[^\w ]+/g,'')
                        .replace(/ +/g,'-')
                        ;
                }";
        if (strlen($id) == 0) {
            $app->add_js($js);
        }
        
        echo $app->render();
    }
    
    public function save_menu_group($id = '') {
        $err_code = 0;
        $err_message = "";

        $app = CApp::instance();
        $org_id = CF::org_id();
        $user = $app->user();
        $db = CDatabase::instance();
        $post = array_merge($_POST, $_GET);
        
        
        if (count($post) > 0 || $post != NULL) {
            $name = carr::get($post, 'name');
            $slug = carr::get($post, 'slug');
            
            if (strlen($id) == 0) {
                // check slug is exist or not, if exist edit to -2
                $check = cdbutils::get_row("SELECT * FROM cms_terms WHERE status > 0 AND slug = ".$db->escape($slug) ." and org_id = " . $org_id . " ORDER BY cms_terms_id DESC");
                if ($check != NULL) {
                    $slug = $check->slug. '-2';
                    $check_dup = substr($slug, -1);
                    if(is_numeric($check_dup)) {
                        $slug = $slug .'-'. ($check_dup+1);
                    }
                }
            }
            
            if (strlen($name) == 0) {
                $err_code++;
                $err_message = "Error, name is required";
            }
            if (strlen($slug) == 0) {
                $err_code++;
                $err_message = "Error, menu name is required";
            }

            $data = array(
                "org_id" => $org_id,
                "name" => $name,
                "slug" => $slug,
            );
            if ($err_code == 0) {
                $db->begin();
                if (strlen($id) > 0) {
                    // EDIT
                    try {
                        $default = array(
                            'updated' => date('Y-m-d H:i:s'),
                            'updatedby' => cobj::get($user, 'username')
                        );
                        $data = array_merge($data, $default);
                        $db->update("cms_terms", $data, array("cms_terms_id" => $id));
                    } catch (Exception $exc) {
                        $err_code++;
                        $err_message = $exc->getMessage();
                    }
                } else {
                    // NEW
                    try {
                        $default = array(
                            'created' => date('Y-m-d H:i:s'),
                            'createdby' => cobj::get($user, 'username')
                        );
                        $data = array_merge($data, $default);
                        $insert = $db->insert("cms_terms", $data);
                        // Insert into term then Insert into taxonomy to know is category or not
                        $term_id = $insert->insert_id();
                        if (strlen($term_id) > 0) {
                            $taxonomy = array(
                                "org_id" => $org_id,
                                "cms_terms_id" => $term_id,
                                "taxonomy" => 'nav_menu'
                            );
                            $taxonomy_data = array_merge($taxonomy, $default);
                            $insert_taxonomy = $db->insert("cms_term_taxonomy", $taxonomy_data);
                            $term_taxonomy_id = $insert_taxonomy->insert_id();
                        }
                    } catch (Exception $exc) {
                        $err_code++;
                        $err_message = $exc->getMessage();
                    }
                }
            }

            if ($err_code > 0) {
                $db->rollback();
                cmsg::add("error", $err_message);
            }

            if ($err_code == 0) {
                $db->commit();
                if (strlen($id) > 0) {
//                    curl::redirect(self::__CONTROLLER.'add');
                    cmsg::add("success", "Update Success [Menu Group][".$name."]");
                }
                else {
                    cmsg::add("success", "Save Success [Menu Group][".$name."]");
                }
            }
        }
        
        curl::redirect(self::__CONTROLLER.'group');
    }
    
    public function menu_group_delete($id) {
        if (strlen($id) == 0) {
            cmsg::add("error", "Not Found!");
            curl::redirect(self::__CONTROLLER.'group');
        }
        
        $err_code = 0;
        $err_message = '';
        
        $app = CApp::instance();
        $user = $app->user();
        $db = CDatabase::instance();
        
        $cms_terms_name = '';
        
        $terms = cdbutils::get_row('SELECT * FROM cms_terms WHERE cms_terms_id=' . $db->escape($id) . ' AND status > 0');
        if ($terms != NULL) {
            // CHECK is in use or not
            $cms_terms_id = $terms->cms_terms_id;
            $cms_terms_name = $terms->name;
            
            // Check in cms_menu
            $cms_menu = cdbutils::get_row("SELECT * FROM cms_menu WHERE cms_terms_id = ".$db->escape($cms_terms_id)." AND status > 0");
            if ($cms_menu != NULL) {
                $err_code++;
                $err_message = "[".$cms_terms_name."] " . clang::__("is in use, please delete menu item first");
            }
            
//            $taxonomy = cdbutils::get_row("SELECT * FROM cms_term_taxonomy WHERE cms_terms_id=".$db->escape($cms_terms_id) . " AND status > 0");
//            
//            if ($taxonomy != NULL) {
//                $cms_term_taxonomy_id = $taxonomy->cms_term_taxonomy_id;
//                
//                $q_relationships = cdbutils::get_row("SELECT * FROM cms_term_relationships WHERE cms_term_taxonomy_id=".$db->escape($cms_term_taxonomy_id)." AND status > 0");
//                if ($q_relationships != NULL) {
//                    $err_code++;
//                    $err_message = "[".$cms_terms_name."] is in use";
//                }
//            }
        }
        else {
            $err_code++;
            $err_message = "Not Found!";
        }
        
        if ($err_code == 0) {
            $data = array(
                "updated" => date("Y-m-d H;i:s"),
                "updatedby" => $user->username,
                "status" => 0
            );
            $db->begin();
            
            try {
                $db->update("cms_terms", $data, array("cms_terms_id"=>$id));
            }
            catch (Exception $e){
                $err_code++;
                $err_message = $e->getMessage();
            }
            
            if ($err_code == 0) {
                $db->commit();
                cmsg::add('success', clang::__("Menu") . " [" . $cms_terms_name . "] " . clang::__("Successfully Deleted") . " !");
            }
            else {
                $db->rollback();
                cmsg::add("error", $err_message);
            }
        }
        
        if ($err_code > 0) {
            cmsg::add("error", $err_message);
        }
        curl::redirect(self::__CONTROLLER.'group');
    }
    
    public function test() {
        echo 'OKE';
        $a = cms::nav_menu('top-menu');
//        $b = cms::nav_menu(4);
        cdbg::var_dump($a);
//        cdbg::var_dump($b);
    }
}
