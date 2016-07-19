<?php

/**
 *
 * @author Hery
 * @since  Nov 16, 2015
 * @license http://piposystem.com Piposystem
 */
class Category_Controller extends CController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $user = $app->user();
        $org_id = CF::org_id();
//        $org_id = null;
        //$app->add_breadcrumb(clang::__("Category"), curl::base() . "cms/category");
        $app->title('Category');

        $category_list = ccms::get_category_type_list();
        
        $widget = $app->add_widget();
        $widget->set_title(clang::__('Product Category List'))->set_icon('filter');
        $form = $widget->add_form();
        $span = $form->add_div()
                ->add_class("row-fluid")
                ->add_div()
                ->add_class("span5");

        $span->add_field()->set_label(clang::__("Category Type"))->add_control('category_type', 'select')->set_list($category_list)->add_class('large');
        if ($org_id == null) {
            $org_control = $span->add_field()
                    ->set_label(clang::__("Org"))
                    ->add_control('org_id', 'org-merchant-select')
                    ->set_none(false)
                    ->add_validation('required');
        }
        
        $actions = $form->add_action_list()->set_style('form-action');
        $actions->add_class('submit-form');
        $actions->add_action()->set_label(clang::__("Show"))->set_submit(true)->set_confirm(false);

        $form->set_ajax_submit(true)->set_ajax_submit_target('table-container')
                ->set_action(curl::base() . "cms/category/table");
        if ($org_id != null) {
            $form->add_listener('ready')->add_handler('submit');
        }

        $app->add_div('table-container');

        echo $app->render();
    }

    public function table() {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $org = $app->org();
        $user = $app->user();
        $org_id = CF::org_id();
//        $org_id  = null;
        
        $err_code = 0;
        $err_message = "";

        $request = array_merge($_GET, $_POST);
        $category_type = carr::get($request, 'category_type');
        $org_id_post = '';
        $org_code = '';
        if ($org_id == null) {
            $org_id_post = carr::get($request, 'org_id');
            if (strlen($org_id_post) == 0) {
                $err_code++;
                $err_message = clang::__('org') . ' is required!';
            }
            if ($err_code == 0) {
                $org_code = cdbutils::get_value("SELECT code FROM org WHERE org_id=".$db->escape($org_id_post) . " AND status > 0");
                $org_code = ' of '.$org_code;
            }
        }

        $app->title(clang::__("Category Type List"));
        $tree = CTreeDB::factory('cms_category')->add_filter('category_type', $category_type);
        if (strlen($org_id_post) > 0) {
            $tree->set_org_id($org_id_post);
        }
        $widget = $app->add_widget()->set_title(clang::__("Category Type") . $org_code);
        
        if ($err_code == 0) {
            $nestable = $widget->add_nestable();
            $widget->clear_both();
            $nestable->set_data_from_treedb($tree)->set_id_key('cms_category_id')->set_value_key('name')->set_input('data_order');
            $nestable->set_applyjs(false);
            $nestable->set_action_style('btn-dropdown');

            $actedit = $nestable->add_row_action('edit');
            $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . "cms/category/edit/{param1}")->set_label(" " . clang::__("Edit") . " " . clang::__("Product Category"));
            $actedit = $nestable->add_row_action('delete');
            $actedit->set_label("")->set_icon("trash")->set_link(curl::base() . "cms/category/delete/{param1}")->set_confirm(true)->set_label(" " . clang::__("Delete") . " " . clang::__("Product Category"));
        }
        else {
            cmsg::add('error', $err_message);
        }
        
        echo $app->render();
    }

    public function add() {
        $this->edit();
    }

    public function edit($id = "") {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $user = $app->user();
        $org_id = CF::org_id();
//        $org_id = null;
        
        $err_code = 0;
        $err_message = '';
        $is_insert = true;

        $title = clang::__("Add Category");

        if (strlen($id) > 0) {
            $title = clang::__("Edit Category");
            $is_insert = false;
        }
        $app->title($title);

        $org = $app->org();
        $org_code = null;
        if ($org) {
            $org_code = $org->org_code;
        }

        $category_type_list = ccms::get_category_type_list();
        $category_type = "";
        $parent_id = "";
        $name = "";
        $url_key = '';
        $description = "";

        $tree = CTreeDB::factory('cms_category')->add_filter('category_type', $category_type);

        $app->add_breadcrumb(clang::__("Category"), curl::base() . "cms/category");
        $imgsrc = curl::base() . 'cresenity/noimage/120/120';
        $widget = $app->add_widget()->set_title($title);
        $form = $widget->add_form()->set_enctype('multipart/form-data');
        $post = $this->input->post();

        if ($post != null) {
            $category_type = carr::get($post, 'category_type');
            $name = carr::get($post, 'name');
            $description = carr::get($post, 'description');
            $parent_id = carr::get($post, 'parent_id');
            $url_key = trim(carr::get($post, 'url_key'));
            
            // condition if user login org_id = null
            $org_post = carr::get($post, 'org_id');
            if ($org_id == null) {
                if (strlen($org_post) > 0) {
                    $org_id = $org_post;
                }
                else {
                    $err_code++;
                    $err_message = clang::__('Org').' is required';
                }
            }
            
            $image = carr::get($_FILES, 'image_name');
            $filename = '';
            $tmp_name = '';
            
            if (strlen($id) == 0) {
                if (isset($image['name'])) {
                    $filename = $image['name'];
                }
                if (isset($image['tmp_name'])) {
                    $tmp_name = $image['tmp_name'];
                }
                if (strlen($filename) == 0) {
                    $err_code++;
                    $err_message = 'Please Insert Image';
                }
                if (strlen($tmp_name) == 0) {
                    $err_code++;
                    $err_message = 'Please Insert Image';
                }
            }

//                if ($parent_id == 0) $parent_id = NULL;

            if ($err_code == 0) {
                $qcheck = " select * from cms_category
                                where url_key=" . $db->escape($url_key) . " and status > 0
                                and category_type=" . $db->escape($category_type)." AND org_id = ".$db->escape($org_id);
                if (strlen($id) > 0)
                    $qcheck .= " and cms_category_id <>" . $db->escape($id) . "";
                $rcheck = $db->query($qcheck);
                if ($rcheck->count() > 0) {
                    $err_message = 'Error,' . ' ' . clang::__('Category') . ' ' . clang::__('is already exist, please try another') . ' ' . clang::__('Category');
                    $err_code++;
                }
            }

            if ($err_code == 0) {
                $resource = CResources::factory("image", "productcategory", $org_code);
                $file_name_generated=null;
                $image_url=null;
                if(isset($_FILES['image_name'])&&isset($_FILES['image_name']['name']) && strlen($_FILES['image_name']['name'])>0) {
                    
                    $filename = $_FILES['image_name']['name'];
                    $path = file_get_contents($_FILES['image_name']['tmp_name']);
                    $file_name_generated = $resource->save($filename, $path);
                    $image_url = $resource->get_url($file_name_generated);
                }

                $data = array(
                    "category_type" => $category_type,
                    //"parent_id" => $parent_id,
                    "name" => $name,
                    "url_key" => $url_key,
                    "description" => $description,
                    "org_id" => $org_id,
                );
                if(strlen($image_url)>0) {
                    $data["image_name"]=$file_name_generated;
                    $data["image_url"]=$image_url;
                    
                }
                if (strlen($parent_id) > 0)
                    $data['parent_id'] = $parent_id;

                try {
                    if (strlen($id) == 0) {
                        $data = array_merge($data, array(
                            "created" => date("Y-m-d H:i:s"),
                            "createdby" => $user->username,
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => $user->username,
                        ));
                        $tree->insert($data, $parent_id);
                        $last_query = $db->last_query();

                        $param = array(
                            'user_id' => $user->user_id,
                            'before' => '',
                            'after' => $data,
                        );
                    } else {
                        if (isset($image['name'])) {
                            $filename = $image['name'];
                        }
                        if (isset($image['tmp_name'])) {
                            $tmp_name = $image['tmp_name'];
                        }
                        $dataa = array(
//                            "cms_category_id" => $cms_category_id,
                            "name" => $name,
                            "url_key" => $url_key,
                            "description" => $description
                        );

                        if (strlen($tmp_name) > 0) {
                            $resource = CResources::factory("image", "productcategory", $org_code);
                            $filename = $_FILES['image_name']['name'];

                            $path = file_get_contents($_FILES['image_name']['tmp_name']);
                            $file_name_generated = $resource->save($filename, $path);
                            $image_url = $resource->get_url($file_name_generated);
                            $dataa = array_merge($dataa, array(
                                "image_name" => $file_name_generated,
                                "image_url" => $image_url,
                            ));
                        }
                        $data = array_merge($dataa, array(
                            "updated" => date("Y-m-d H:i:s"),
                            "updatedby" => $user->username,
                        ));

                        $before = cdbutils::get_row('select * from cms_category where cms_category_id = ' . $db->escape($id));
                        $param = array(
                            'user_id' => $user->user_id,
                            'before' => $before,
                            'after' => $data,
                        );
                        $tree->update($id, $data, $parent_id);
                        $last_query = $db->last_query();
                    }
                } catch (Exception $e) {
                    $err_code++;
                    $err_message = "Error, call administrator..." . $e->getMessage();
                }
            }

            if ($err_code == 0) {
                if ($id > 0) {
//                    clog::activity($param, 'Edit_Product_Category', clang::__("Product Category") . " [" . $name . "] " . clang::__("Successfully Modified") . " !");
                    cmsg::add('success', clang::__("Category") . " [" . $name . "] " . clang::__("Successfully Modified") . " !");
                    curl::redirect("cms/category");
                } else {
//                    clog::activity($param, 'Add_Product_Category', clang::__("Product Category") . " [" . $name . "] " . clang::__("Successfully Added") . " !");
                    cmsg::add('success', clang::__("Category") . " [" . $name . "] " . clang::__("Successfully Added") . " !");
                    curl::redirect("cms/category/add");
                }
            } else {
//                    cmsg::add('error', clang::__("Product Category") . " [" . $name . "] " . $err_message . " !");
                cmsg::add("error", $err_message);
            }
        } else if (strlen($id) > 0) {
            $q = " SELECT * FROM cms_category WHERE cms_category_id = " . $db->escape($id);
            $row = cdbutils::get_row($q);
            if ($row != NULL) {
                $category_type = $row->category_type;
                $name = $row->name;
                $file_name_generated = $row->image_name;
                $image_url = $row->image_url;
                $url_key = $row->url_key;
                $description = $row->description;
                $parent_id = $row->parent_id;
            }
        }

        $div_row = $form->add_div()->add_class("row-fluid");
        $span = $div_row->add_div()->add_class("span7");

        $image_div_right = $div_row->add_div()->add_class('span5');
        $info = $image_div_right->add_div()->add_class('alert alert-info');
        $info->add('<h4>' . clang::__('Information') . '</h4>');
        $info->add('<ul><li>Ukuran Gambar <br>width : <b>386px &nbsp</b>height: <b>469px</b></li></ul>');
        $product_category = array();
        
        if (strlen($id) > 0) {
            if ($err_code > 0)
//                $product_type_name = cdbutils::get_value('select name from product_type where product_type_id = ' . $db->escape($product_type_id) . ' and status > 0 ');
//            $span->add_field()->set_label(clang::__("Category Type"))
//                    ->add_control('category_type', 'label')->add_validation(null)
//                    ->set_value($product_type_name)->add_transform('uppercase');

            $tree = CTreeDB::factory('cms_category')->add_filter('category_type', $category_type);
            $product_category = $product_category + $tree->get_list('&nbsp;&nbsp;&nbsp;&nbsp;');
            if($id!=null&&isset($product_category[$id])) {
                unset($product_category[$id]);
            }
            //$product_category = array('' => 'NONE') + $product_category;

            $parent_name = cdbutils::get_value('select name from cms_category where status > 0 and cms_category_id = ' . $db->escape($parent_id) . ' ');
            $span->add_control('parent_id', 'hidden')->set_value($parent_id);
            $span
                    ->add_field()
                    ->set_label(clang::__('Parent'))
                    ->add_control('parent_name', 'text')
                    ->add_validation(null)
                    ->set_value($parent_name)
                    ->set_disabled(true)
            ;
        }
        else {
            $product_type_control = $span->add_field()
                    ->set_label("<span style='color:red;'>*</span> " . clang::__("Category Type"))
                    ->add_control("category_type", "select")
                    ->set_list($category_type_list)
                    ->set_value($category_type);

            $tree = CTreeDB::factory('cms_category')->add_filter('category_type', $category_type);
            $product_category = $product_category + $tree->get_list('&nbsp;&nbsp;&nbsp;&nbsp;');
            //$product_category = array('' => 'NONE') + $product_category;

            $product_category_control = $span->add_div('div_product_category_control');

            $product_category_control->add_field()
                    ->set_label(clang::__("Parent"))
                    ->add_control("parent_id", "select")
                    ->add_validation(null)
                    ->set_value($parent_id)
                    ->set_list($product_category);
                    
                    

            $product_type_control->add_listener('change')->add_handler('reload')
                    ->set_target('div_product_category_control')
                    ->set_url(curl::base() . 'cms/category/reload_category_parent')
                    ->add_param_input(array('category_type'));

            if (strlen($parent_id) == 0) {
                $product_type_control->add_listener('ready')->add_handler('reload')
                        ->set_target('div_product_category_control')
                        ->set_url(curl::base() . 'cms/category/reload_category_parent')
                        ->add_param_input(array('category_type', 'parent_id'));
            }
        }
        
        $span->add_field()
                ->set_label("<span style='color:red;'>*</span> " . clang::__("Category Name"))
                ->add_control("name", "text")
                ->add_validation('required')
                ->set_value($name);

        if ($is_insert) {
            if ($org_id == null) {
                $org_control = $span->add_field()->set_label("<span style='color:red;'>*</span> ". clang::__("Org"))->add_control('org_id', 'org-merchant-select')->add_validation('required');
            }
            
            $span
                    ->add_field()
                    ->set_label(clang::__("Url Key"))
                    ->add_control('url_key', 'text')
                    ->set_value($url_key)
                    ->set_placeholder("Auto");
            $span
                    ->add_field()
                    ->set_label('<span style="color: red;">*</span> ' . clang::__("Image"))
                    ->add_control('image_name', 'image')
                    ->set_imgsrc($imgsrc)
                    ->set_maxwidth(386)
                    ->set_maxheight(469);


        } else {
            if ($org_id == null) {
                $org_control_display = $span->add_field()->set_label("<span style='color:red;'>*</span> ". clang::__("Org"))->add_control('org_name', 'text');
                $org_control = $span->add_control('org_id', 'hidden');
                $org_name = '';
                if (strlen($id) > 0) {
                    $q = "SELECT  o.code org_code, o.name org_name, c.* FROM cms_category c LEFT JOIN org o ON o.org_id = c.org_id WHERE c.cms_category_id =" . $db->escape($id);
                    $row = cdbutils::get_row($q);
                    if ($row != NULL) {
                        $org_id = cobj::get($row, 'org_id');
                        $org_name = cobj::get($row, 'org_name');
                        $org_control_display->set_readonly(true);
                    }
                }
                $org_control->set_value($org_id);
                $org_control_display->set_value($org_name);
            }
            
            $span
                    ->add_field()
                    ->set_label('<span style="color: red;">*</span> ' . clang::__("Image"))
                    ->add_control('image_name', 'image')
                    ->set_imgsrc($image_url)
                    ->set_maxwidth(386)
                    ->set_maxheight(469);

            $span
                    ->add_field()
                    ->set_label(clang::__("Url Key"))
                    ->add_control('url_key_show', 'text')
                    ->set_value($url_key)
                    ->set_placeholder("Auto")
                    ->set_disabled(true);

            $span->add_control('url_key', 'hidden')->set_value($url_key);
        }

        $span->add_field()
                ->set_label(clang::__("Description"))
                ->add_control("description", "textarea")
                ->set_value($description);

        $action = $form->add_action_list()->add_action()
                        ->set_label(clang::__("Submit"))->set_submit(true)->set_confirm(true);

        if ($is_insert) {
            $js = "jQuery('#name').keyup(function(){
                        var Title = jQuery(this).val();
                        jQuery('#url_key').val(convertToSlug(Title));
                    });
                    function convertToSlug(Text)
                    {
                        return Text
                            .toLowerCase()
                            .replace(/[^\w ]+/g,'')
                            .replace(/ +/g,'-')
                            ;
                    }";
            $app->add_js($js);
        }

        echo $app->render();
    }

    public function reload_category_parent() {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $category_type = $_GET['category_type'];
        $parent_id = '';

        $tree = CTreeDB::factory('cms_category')->add_filter('category_type', $category_type);
        
        $product_category = array();
        $product_category = $product_category + $tree->get_list('&nbsp;&nbsp;&nbsp;&nbsp;');
        //$product_category = array('' => 'NONE') + $product_category;

        $app->add_field()
                ->set_label(clang::__("Parent"))
                ->add_control("parent_id", "select")
                ->add_validation(null)
                ->set_value($parent_id)
                ->set_list($product_category);
                

        echo $app->render();
    }

    public function delete($id = "") {
        if (strlen($id) == 0) {
            curl::redirect("cms/category");
        }
        $app = CApp::instance();
        $user = $app->user();
        $org_id = CF::org_id();
        $db = CDatabase::instance();
        $error = 0;
        $error_message = 0;

//        $before = cdbutils::get_row('select * from cms_category where cms_category_id = ' . $db->escape($id)." and org_id = ".$db->escape($org_id));
//        $param = array('user_id' => $user->user_id, 'before' => (array) $before, 'after' => '',);

        if ($error == 0) {
            $q = "SELECT * FROM cms_category
                      WHERE status>0 AND parent_id = " . $db->escape($id);
            $r = $db->query($q);

            if (count($r) > 0) {
                $error++;
                $error_message = "Category is already as Parent";
            }
        }

//        if ($error == 0) {
//            $q = "SELECT * FROM product
//                      WHERE status>0 AND product_category_id = " . $db->escape($id);
//            $r = $db->query($q);
//
//            if (count($r) > 0) {
//                $error++;
//                $error_message = "Product Category is already exist in Product";
//            }
//        }

        if ($error == 0) {
            try {
                $db->update("cms_category", array("status" => 0, "updated" => date("Y-m-d H:i:s"),
                    "updatedby" => $user->username), array("cms_category_id" => $id, "org_id"=>$org_id));
            } catch (Exception $e) {
                $error++;
                $error_message = clang::__("system_delete_fail") . $e->getMessage();
            }
        }

        if ($error == 0) {
//            clog::activity($param, 'Delete_Product_Category', clang::__("Product Category") . " [" . $before->name . "] " . clang::__("Successfully Deleted") . " !");
            cmsg::add('success', clang::__("Category") . " " . clang::__("Successfully Deleted") . " !");
        } else {
//                cmsg::add('error', clang::__("Product Category") . " [" . $before->name . "] " . $err_message . " !");
            cmsg::add("error", $error_message);
        }
        curl::redirect("cms/category");
    }

    public function rebuild() {
        $app = CApp::instance();
        $user = $app->user();

        $tree = CTreeDB::factory('cms_category')->add_filter('category_type', 'default');
        $tree->rebuild_tree_all();
    }

}
