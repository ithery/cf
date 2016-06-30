<?php

/**
 * Description of post
 *
 * @author Ecko Santoso
 * @since 08 Sep 15
 */
class Page_Controller extends CController {
    
    CONST __CONTROLLER = "cms/page/";

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = CApp::instance();
        $org_id = CF::org_id();
        $app->title(clang::__("Page List"));
        
        $post = ccms::page();
        
         $q = "SELECT
                tr.cms_term_taxonomy_id, tt.cms_terms_id, t.name term_name, t.slug term_slug,
                p.*
               FROM cms_post p
               LEFT JOIN cms_term_relationships tr ON tr.cms_post_id=p.cms_post_id
               LEFT JOIN cms_term_taxonomy tt ON tt.cms_term_taxonomy_id = tr.cms_term_taxonomy_id
               LEFT JOIN cms_terms t ON t.cms_terms_id=tt.cms_terms_id
               WHERE p.status > 0 and p.org_id = " . $org_id . "
               AND p.post_type = 'page'";
        
        $table = $app->add_table('page')->set_quick_search(true);
        $table->add_column('post_title')->set_label(clang::__("Title"));
        //$table->add_column('post_excerpt')->set_label(clang::__("Content"));
        $table->add_column('post_status')->set_label(clang::__("Status"));
        $table->add_column('post_name')->set_label(clang::__("Url"));
        $table->add_column('updated')->set_label(clang::__("Updated"))->add_transform("format_datetime");
        $table->add_column('updatedby')->set_label(clang::__("Updated By"));
        //$table->set_data_from_array($post)->set_key('cms_post_id');
        $table->set_data_from_query($q)->set_key('cms_post_id');;
        $table->set_title(clang::__("Page"));
        $table->set_apply_data_table(true);
        $table->set_ajax(true);
        $table->set_quick_search(true);
        
        if (cnav::have_permission('edit_page')) {
            $actedit = $table->add_row_action('edit');
            $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . self::__CONTROLLER."edit/{cms_post_id}")->set_label(clang::__('Edit'));
        }
        if (cnav::have_permission('delete_page')) {
            $actdelete = $table->add_row_action('delete');
            $actdelete->set_label("")->set_icon("trash")->set_link(curl::base() . self::__CONTROLLER ."delete/{cms_post_id}")->set_confirm(true)->set_label(clang::__('Delete'));
        }
        $table->set_action_style("btn-dropdown");
        
        echo $app->render();
    }

    public function add() {
        $this->edit();
    }

    public function edit($id = '') {
        $err_code = 0;
        $err_message = "";

        $app = CApp::instance();
        $org_id = CF::org_id();
        $role = $app->role();
        $user = $app->user();
        $db = CDatabase::instance();

        $post = array_merge($_GET, $_POST);

        $title = clang::__("Add New Page");
        if (strlen($id) > 0) {
            $title = clang::__("Edit Page");
        }
        $app->title($title);

        // DECLARE
        $status_list = ccms::post_status();
        $post_title = "";
        $post_name = "";
        $post_content = "";
        $post_status = "";
//        $item_image = "";
        $type = 'page';
        $template = "";
        $cannot_delete = 0;

		if (strlen($id) > 0) {
            $page = ccms::page($id);
			
            $post_title = cobj::get($page, 'post_title');
            $post_name = cobj::get($page, 'post_name');
            $post_content = cobj::get($page, 'post_content');
            $post_status = cobj::get($page, 'post_status');
            $template = cobj::get($page, 'template');
            $cannot_delete = cobj::get($page, 'cannot_delete');
            
            $template = cobj::get($page, 'template');
        }
		
        // PROCESS
        if (count($post) > 0 || $post != NULL) {
            $db->begin();
            
//                $filename = "";
//                if (isset($_FILES["item_image"])) {
//                    $filename = $_FILES["item_image"]["name"];
//                }
//                $filename = cutils::sanitize($filename, true);
//                $filename_product = $filename;
//                $item_image = basename(stripslashes($filename));
                
                
                if (isset($post['cannot_delete'])) {
                    $cannot_delete = 1;
                }
                $post_title = carr::get($post, 'post_title');
                $post_name = carr::get($post, 'post_name');
                $post_content = carr::get($post, 'post_content');
                $template = carr::get($post, 'template');
                $post_excerpt = ccms::excerpt_paragraph($post_content, 50);
                
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
                
                if (strlen($post_title) == 0) {
                    $err_code++;
                    $err_message = "Title is required.";
                }
                
                if (strlen($post_name) == 0) {
                    $err_code++;
                    $err_message = "Url is required.";
                }
                
                $data_post = array(                    
                    "org_id" => $org_id,
                    "post_title" => carr::get($post, 'post_title'),
                    "post_name" => carr::get($post, 'post_name'),
                    "post_type" => "page",
                    "post_content" => $post_content,
                    "post_status" => carr::get($post, 'post_status'),
                    "post_excerpt" => $post_excerpt,
                    "template" => $template,
                    "cannot_delete" => $cannot_delete,
                );
            if (strlen($id) > 0) {
//                $query = "select " .
//                        " file_location, filename" .
//                        " " .
//                        " from cms_post where cms_post_id = " . $db->escape($id);
//                $get_file_image = $db->query($query);
//                if ($get_file_image->count() > 0) {
//                    $row = $get_file_image[0];
//                    $older_image = $row->filename;
//                }

                // EDIT
                $default = array(
                    'updated' => date('Y-m-d H:i:s'),
                    'updatedby' => cobj::get($user, 'username')
                );
                try {
                    $data = array_merge($data_post, $default);
                    $db->update("cms_post", $data, array("cms_post_id" => $id));
//                    if (strlen($filename) > 0) {
//                        $path = cimage::get_image_path('cmsb2c_image'.DS . $type, $id, 'original');
//                        $path = preg_replace('#\\\#ims', '/', $path);
//                        cimage::create_image_folder("cmsb2c_image/" . $type, $id);
//                        cimage::delete_all_image("cmsb2c_image/" . $type, $id, $older_image);
//                        $filename = cupload::save("item_image", $filename, $path);
//                        cimage::resize_image("cmsb2c_image/" . $type, $id, $filename);
//                        $path_url = cimage::get_upload_url('cmsb2c_image/' . $type, $id, 'original', $filename_product);
//
//                        $before_gallery = cdbutils::get_row('select * from cms_post where cms_post_id = ' . $db->escape($id) . ' and status > 0');
//                        $path = substr($path, 0, -1);
//                        $data = array('file_location' => $path, 'filename' => $filename_product, 'url_location' => $path_url);
//                        $db->update("cms_post", $data, array("cms_post_id" => $id));
//                    }
                }
                catch (Exception $e){
                    $err_code++;
                    $err_message = $e->getMessage();
                }
            } else {
                // INSERT NEW
                $default = array(
                    'created' => date('Y-m-d H:i:s'),
                    'createdby' => cobj::get($user, 'username')
                );

                try {
                    $data = array_merge($data_post, $default);
                    $insert_post = $db->insert("cms_post", $data);
                    $cms_post_id = $insert_post->insert_id();
                    
                     // Added image on page
//                    if (strlen($filename) > 0) {
//                        $path = cimage::get_image_path('cmsb2c_image/' . $type, $cms_post_id, 'original');
//                        $path = preg_replace('#\\\#ims', '/', $path);
//                        cimage::create_image_folder("cmsb2c_image/" . $type, $cms_post_id);
//                        cimage::delete_all_image("cmsb2c_image/" . $type, $cms_post_id, $item_image);
//                        $filename = cupload::save("item_image", $filename, $path);
//                        cimage::resize_image("cmsb2c_image/" . $type, $cms_post_id, $filename);
//                        $path_url = cimage::get_upload_url('cmsb2c_image/' . $type, $cms_post_id, 'original', $filename_product);
//                    }
//
//                    if ($err_code == 0) {
//                        if (strlen($filename) > 0) {
//                            $before_gallery = cdbutils::get_row('select * from cms_post where cms_post_id = ' . $db->escape($cms_post_id));
//                            $path = substr($path, 0, -1);
//                            $data = array(
//                                'file_location' => $path,
//                                'filename' => $filename_product,
//                                'url_location' => $path_url
//                            );
//                            $db->update("cms_post", $data, array("cms_post_id" => $cms_post_id));
//                        }
//                    }
                    
                } catch (Exception $e) {
                    $err_code++;
                    $err_message = $e->getMessage();
                }
            }
            
            if ($err_code > 0) {
                $db->rollback();
                cmsg::add("error", $err_message);
            }
            
            if ($err_code == 0) {
                $db->commit();
                if (strlen($id) > 0) {
                    cmsg::add("success", "Update Success");
                    curl::redirect(self::__CONTROLLER);
                }
                else {
                    cmsg::add("success", "Save Success");
                    curl::redirect(self::__CONTROLLER."add");
                }
            }
        }
        
        

        // BUILD UI
        $form = $app->add_form()->set_enctype('multipart/form-data');
        $div = $form->add_div()->add_class('row');
        $div_left = $div->add_div()->add_class('span8');
        $widget_left = $div_left->add_widget();
        $div_right = $div->add_div()->add_class('span4');
        $widget_right = $div_right->add_widget();
        
        // DIV LEFT
        if ($org_id == null) {
            $widget_left->add_field()->set_label(clang::__("Org").' <red>*</red>')->add_control('org_id', 'org-merchant-select')->set_value($org_id)->add_validation('required');
        }
        $widget_left->add_field()->set_label(clang::__("Page Title").' <red>*</red>')->add_control('post_title', 'text')->set_value($post_title)->add_validation('required');
        $widget_left->add_field()->set_label(clang::__("Url").' <red>*</red>')->add_control('post_name', 'text')->set_value($post_name)->set_placeholder("Auto")->add_validation('required');
        $widget_left->add_field()->set_label(clang::__("Page Content"))->add_control('post_content', 'ckeditor')->set_value($post_content);
        // DIV RIGHT
        $template_list = ccms::get_template_list();
        $widget_right->add_field()->set_label(clang::__("Template"))->add_control('template', 'select')->set_value($template)->set_list($template_list)->add_class('large');
        $widget_right->add_field()->set_label(clang::__("Page Status"))->add_control('post_status', 'select')->set_value($post_status)->set_list($status_list)->add_class('large');
//        $widget_right->add_field()
//                ->set_label(clang::__("Featured Image"))
//                ->add_control('item_image', 'image')
//                ->set_imgsrc($imgsrc)
//                ->set_maxwidth(150)
//                ->set_maxheight(100);
        if ($role != NULL) {
            if ($role->parent_id == NULL) {
                $field_cannot_delete = $widget_right->add_field()->set_label(clang::__("Cannot delete"))->add_control('cannot_delete', 'checkbox');
                if ($cannot_delete == 1) {
                    $field_cannot_delete->set_checked(true);
                }
            }
        }

        $form->add_action_list()->add_action()->set_label(clang::__("Submit"))->set_confirm(true)->set_submit(true);

        $js = "jQuery('#post_title').keyup(function(){
                    var Title = jQuery(this).val();
                    jQuery('#post_name').val(convertToSlug(Title));
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
        echo $app->render();
    }
    
    public function delete($id = "") {
        if (strlen($id) == 0) {
            cmsg::add("error", "Not Found!");
            curl::redirect(self::__CONTROLLER);
        }
        $app = CApp::instance();
        $user = $app->user();
        $db = CDatabase::instance();
        $post = cdbutils::get_row('SELECT * FROM cms_post WHERE cms_post_id=' . $db->escape($id) . ' AND status > 0');
        
        $err_code = 0;
        $err_message = "";
        
        if ($post == NULL) {
            $err_code++;
            $err_message = 'Not found';
        }
        
        // Check if cannot delete
        if ($err_code == 0) {
            if ($post->cannot_delete == 1) {
                $err_code++;
                $err_message = "Cannot delete, Page [".$post->post_name."] is required!";
            }
        }
        
        if ($err_code == 0) {
            $q = cdbutils::get_row("SELECT * FROM cms_menu WHERE status > 0 AND menu_item_object = 'page' AND menu_item_object_id = ".$db->escape($id));
            if ($q != NULL) {
                $err_code++;
                $err_message = "Cannot delete, Page [".$post->post_name."] is in use on menu!";
            }
        }

        if ($err_code == 0) {
            try {
                $db->begin();
                $db->update(
                        "cms_post", 
                        array("status" => 0, "updated" => date("Y-m-d H:i:s"),"updatedby" => $user->username), 
                        array("cms_post_id" => $id)
                        );
                $q_relationships = "SELECT * FROM cms_term_relationships WHERE cms_post_id = ".$db->escape($id);
                $r_relationships = cdbutils::get_row($q_relationships);
                if ($r_relationships != NULL) {
                    $rel_id = $r_relationships->cms_term_relationships_id;
                    $db->update(
                            "cms_term_relationships",
                            array("status" => 0, "updated" => date("Y-m-d H:i:s"),"updatedby" => $user->username), 
                            array("cms_term_relationships_id"=>$rel_id)
                            );
                }
            } catch (Exception $e) {
                $err_code++;
                $err_message = clang::__("system_delete_fail") . $e->getMessage();
            }
        }

        if ($err_code == 0) {
            $db->commit();
            cmsg::add('success', clang::__("Post") . " [" . $post->post_title . "] " . clang::__("Successfully Deleted") . " !");
        } else {
            $db->rollback();
            cmsg::add('error', $err_message);
        }
        curl::redirect(self::__CONTROLLER);
    }

}
