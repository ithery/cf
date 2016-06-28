<?php

/**
 * Description of post
 *
 * @author Ecko Santoso
 * @since 08 Sep 15
 */
class Post_Controller extends CController {

    CONST __CONTROLLER = "cms/post/";

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = CApp::instance();
        $org_id = CF::org_id();
        $app->title(clang::__("Post List"));
        $db = CDatabase::instance();
        $post = ccms::post();

        $q = "SELECT
                tr.cms_term_taxonomy_id, tt.cms_terms_id, t.name term_name, t.slug term_slug,
                tc.name as category_name,
                p.*
               FROM cms_post p
               LEFT JOIN cms_term_relationships tr ON tr.cms_post_id=p.cms_post_id
               LEFT JOIN cms_term_taxonomy tt ON tt.cms_term_taxonomy_id = tr.cms_term_taxonomy_id
               LEFT JOIN cms_terms t ON t.cms_terms_id=tt.cms_terms_id
               LEFT JOIN cms_category tc ON tc.cms_category_id=p.cms_category_id
               WHERE p.status > 0 
               AND p.post_type <> 'page' and p.post_type <> 'nav_menu_item'";

        if(strlen($org_id)>0&&$org_id!=0) {
            $q.= "and p.org_id = " . $db->escape($org_id) ;
        }
        $table = $app->add_table('post')->set_quick_search(true);
        $table->add_column('post_type')->set_label(clang::__("Type"))->add_transform('uppercase');
        $table->add_column('post_title')->set_label(clang::__("Title"));
        $table->add_column('category_name')->set_label(clang::__("Category"));
        $table->add_column('post_status')->set_label(clang::__("Status"));
        $table->add_column('post_name')->set_label(clang::__("Url"));
        $table->add_column('updated')->set_label(clang::__("Updated"))->add_transform('format_datetime');
        $table->add_column('updatedby')->set_label(clang::__("Updated By"));
//        $table->set_data_from_array($post)->set_key('cms_post_id');
        $table->set_data_from_query($q)->set_key('cms_post_id');
        $table->set_title(clang::__("Post"));
        $table->set_ajax(true);

        if (cnav::have_permission('edit_post')) {
            $actedit = $table->add_row_action('edit');
            $actedit->set_label("")->set_icon("pencil")->set_link(curl::base() . self::__CONTROLLER . "edit/{cms_post_id}")->set_label(clang::__('Edit'));
        }

        if (cnav::have_permission('delete_post')) {
            $actdelete = $table->add_row_action('delete');
            $actdelete->set_label("")->set_icon("trash")->set_link(curl::base() . self::__CONTROLLER . "delete/{cms_post_id}")->set_confirm(true)->set_label(clang::__('Delete'));
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
        $org_code = CF::org_code();

        $app = CApp::instance();
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        $user = $app->user();
        

        $post = array_merge($_GET, $_POST);

        $title = clang::__("Add New Post");
        if (strlen($id) > 0) {
            $title = clang::__("Edit Post");
        }
        $app->title($title);

        // DECLARE
        $status_list = ccms::post_status();
        $options = array();
        
        $post_title = "";
        $post_name = "";
        $post_content = "";
        $post_type = "";
        $post_status = "";
        $cms_terms_id = "";
        $item_image = "";
        $type = 'post';
        $template = "";
        $cms_tag_value = array();
        
        
        if (strlen($id) > 0) {
            $q_post = "SELECT * FROM cms_post WHERE status > 0 AND cms_post_id = " . $db->escape($id);
            $r_post = cdbutils::get_row($q_post);
            if ($r_post != NULL) {
                $cms_post_id = $r_post->cms_post_id;
                $post_content = $r_post->post_content;
                $post_title = $r_post->post_title;
                $post_name = $r_post->post_name;
                $post_excerpt = $r_post->post_excerpt;
                $post_status = $r_post->post_status;
                $post_type = $r_post->post_type;
                $template = $r_post->template;

                $q_val_tag = "select distinct tag from cms_post_tag where cms_post_id=" . $db->escape($id) . "  ";
                if (strlen($org_id) > 0) {
                    $q_val_tag .=" and org_id=" . $db->escape($org_id);
                }

                $cms_tag_value = cdbutils::get_array($q_val_tag);

                $q_relation = "SELECT * FROM cms_term_relationships WHERE status > 0 AND cms_post_id = " . $db->escape($cms_post_id);
                $r_relation = cdbutils::get_row($q_relation);
                if ($r_relation != NULL) {
                    $cms_term_taxonomy_id = $r_relation->cms_term_taxonomy_id;
                    if (strlen($cms_term_taxonomy_id) > 0) {
                        $q_term_taxonomy = "SELECT * FROM cms_term_taxonomy WHERE status > 0 AND cms_term_taxonomy_id =" . $db->escape($cms_term_taxonomy_id);
                        $r_term_taxonomy = cdbutils::get_row($q_term_taxonomy);
                        if ($r_term_taxonomy != NULL) {
                            $cms_terms_id = $r_term_taxonomy->cms_terms_id;
                        }
                    }
                }

                if (strlen($r_post->file_location) > 0) {
                    $item_image = curl::base() . 'upload/cmsb2c_image/' . $type . '/' . $id . '/small/' . $r_post->filename;
                }
            }
        }
        // PROCESS
        if (count($post) > 0 || $post != NULL) {
            //get variable
            $db->begin();
            $filename = "";
            if (isset($_FILES["item_image"])) {
                $filename = $_FILES["item_image"]["name"];
            }

            $filename = cutils::sanitize($filename, true);
            $filename_product = $filename;
            $item_image = basename(stripslashes($filename));

            
            $post_content = carr::get($post, 'post_content');
            $post_excerpt = ccms::excerpt_paragraph($post_content, 50);
            $cms_category_id = carr::get($post, 'cms_category_id');
            $post_title = carr::get($post, 'post_title');
            $post_name_ori = cstr::sanitize($post_title);
            $template = carr::get($post, 'template');
            $post_type = carr::get($post, 'post_type');
            $post_status = carr::get($post, 'post_status');
            $cms_terms_id = null;
            //check for exists urlkey
            $is_duplicate = true;
            $i = 0;
            $post_name = $post_name_ori;
            while ($is_duplicate) {
                $post_name = $post_name_ori;
                if ($i > 0) {
                    $post_name = $post_name_ori . "-" . ($i + 1);
                }
                $q = "select * from cms_post where post_name=" . $db->escape($post_name);
                if (strlen($id) > 0) {
                    $q .= " and cms_post_id <> " . $db->escape($id);
                }
                $qcheck = cdbutils::get_row($q);
                if ($qcheck != null) {
                    $i++;
                }
                else {
                    $is_duplicate = false;
                }
            }
            
            //validation
            if($err_code==0) {
                if(strlen($post_title)==0) {
                    $err_code++;
                    $err_message="Post Title Empty";
                }
            }
            //process
            if($err_code==0) {
                if(strlen($cms_category_id)==0) $cms_category_id=null;
                $data_post = array(
                    "org_id" => $org_id,
                    "post_title" => $post_title,
                    "post_name" => $post_name,
                    "template" => $template,
                    "post_type" => $post_type,
                    "post_content" => $post_content,
                    "post_status" => $post_status,
                    "post_excerpt" => $post_excerpt,
                    "cms_category_id" => $cms_category_id,
                );
                if (strlen($id) > 0) {
                    $query = "select " .
                            " file_location, filename" .
                            " " .
                            " from cms_post where cms_post_id = " . $db->escape($id);
                    $get_file_image = $db->query($query);
                    if ($get_file_image->count() > 0) {
                        $row = $get_file_image[0];
                        $older_image = $row->filename;
                    }

                    // EDIT
                    $default = array(
                        'updated' => date('Y-m-d H:i:s'),
                        'updatedby' => cobj::get($user, 'username')
                    );
                    try {
                        $data = array_merge($data_post, $default);
                        $db->update("cms_post", $data, array("cms_post_id" => $id));

                        if (strlen($cms_terms_id) > 0 && is_numeric($cms_terms_id)) {
                            $q = cdbutils::get_row("SELECT * FROM cms_term_taxonomy WHERE status > 0 AND cms_terms_id=" . $db->escape($cms_terms_id));
                            if ($q != NULL) {
                                $term_relationships_data = array(
                                    "cms_term_taxonomy_id" => $q->cms_term_taxonomy_id,
                                );
                                $data_relationships = array_merge($term_relationships_data, $default);
                                $update_relationship = $db->update("cms_term_relationships", $data_relationships, array('cms_post_id' => $id));
                            } else {
                                $err_code++;
                                $err_message = "Error, term_taxonomy not found!";
                            }
                        }


                        if (strlen($filename) > 0) {
                            $path = cimage::get_image_path('cmsb2c_image/' . $type, $id, 'original');
                            $path = preg_replace('#\\\#ims', '/', $path);
                            cimage::create_image_folder("cmsb2c_image/" . $type, $id);
                            cimage::delete_all_image("cmsb2c_image/" . $type, $id, $older_image);
                            $filename = cupload::save("item_image", $filename, $path);
                            cimage::resize_image("cmsb2c_image/" . $type, $id, $filename);
                            $path_url = cimage::get_upload_url('cmsb2c_image/' . $type, $id, 'original', $filename_product);

                            $before_gallery = cdbutils::get_row('select * from cms_post where cms_post_id = ' . $db->escape($id) . ' and status > 0');
                            $path = substr($path, 0, -1);
                            $data = array('file_location' => $path, 'filename' => $filename_product, 'url_location' => $path_url);
                            $db->update("cms_post", $data, array("cms_post_id" => $id));
                        }
                    } catch (Exception $e) {
                        $err_code++;
                        $err_message = $e->getMessage();
                    }
                } 
                else {
                    // INSERT NEW
                    $default = array(
                        'created' => date('Y-m-d H:i:s'),
                        'createdby' => cobj::get($user, 'username')
                    );

                    try {
                        $data = array_merge($data_post, $default);
                        $insert_post = $db->insert("cms_post", $data);
                        $cms_post_id = $insert_post->insert_id();

                        // Jika ada cms_terms_id maka input juga ke cms_term_relationship sebagai penghubung antara post dan term melalui term_taxonomy
                        if (strlen($cms_terms_id) > 0 && is_numeric($cms_terms_id)) {
                            // get cms_term_taxonomy_id where cms_terms_id, then insert into cms_term_relationship
                            $q = cdbutils::get_row("SELECT cms_term_taxonomy_id FROM cms_term_taxonomy WHERE status > 0 AND cms_terms_id=" . $db->escape($cms_terms_id));
                            if ($q != NULL) {
                                $term_data = array(
                                    "cms_term_taxonomy_id" => $q->cms_term_taxonomy_id,
                                    "cms_post_id" => $cms_post_id,
                                );
                                $data_term = array_merge($term_data, $default);
                                $insert_relationship = $db->insert("cms_term_relationships", $data_term);
                                $cms_term_relationship_id = $insert_relationship->insert_id();
                            } else {
                                $err_code++;
                                $err_message = "Error, term_taxonomy not found!";
                            }
                        }

                        // Added image on post
                        if (strlen($filename) > 0) {
                            $path = cimage::get_image_path('cmsb2c_image/' . $type, $cms_post_id, 'original');
                            $path = preg_replace('#\\\#ims', '/', $path);
                            cimage::create_image_folder("cmsb2c_image/" . $type, $cms_post_id);
                            cimage::delete_all_image("cmsb2c_image/" . $type, $cms_post_id, $item_image);
                            $filename = cupload::save("item_image", $filename, $path);
                            cimage::resize_image("cmsb2c_image/" . $type, $cms_post_id, $filename);
                            $path_url = cimage::get_upload_url('cmsb2c_image/' . $type, $cms_post_id, 'original', $filename_product);
                        }

                        if ($err_code == 0) {
                            if (strlen($filename) > 0) {
                                $before_gallery = cdbutils::get_row('select * from cms_post where cms_post_id = ' . $db->escape($cms_post_id));
                                $path = substr($path, 0, -1);
                                $data = array(
                                    'file_location' => $path,
                                    'filename' => $filename_product,
                                    'url_location' => $path_url
                                );
                                $db->update("cms_post", $data, array("cms_post_id" => $cms_post_id));
                            }
                        }
                    } catch (Exception $e) {
                        $err_code++;
                        $err_message = $e->getMessage();
                    }
                }
            }

            // Refresh tag on post
            if ($err_code == 0) {
                if (strlen($id) > 0) {
                    $cms_post_id = $id;
                }
                $post_tag = carr::get($post, 'post_tag');
                if (!is_array($post_tag) && is_string($post_tag)) {
                    $post_tag = explode(",", $post_tag);
                }
                if (is_array($post_tag)) {
                    $db->delete('cms_post_tag', array('cms_post_id' => $cms_post_id));
                    foreach ($post_tag as $tag) {
                        if (strlen($tag) == 0)
                            continue;
                        $data = array(
                            'tag' => $tag,
                            'cms_post_id' => $cms_post_id,
                            'org_id' => $org_id,
                            'created' => date('Y-m-d H:i:s'),
                            'createdby' => cobj::get($user, 'username'),
                            'updated' => date('Y-m-d H:i:s'),
                            'updatedby' => cobj::get($user, 'username'),
                        );
                        $db->insert('cms_post_tag', $data);
                    }
                }
            }

            //processing custom fields
            if($err_code==0) {
                $options = array(
                    "post_id"=>$cms_post_id
                );
                $fields = ccms::get_custom_fields($options);
                
                foreach($fields as $k=>$v) {
                    $field_type = carr::get($v,'type');
                    $field_name = carr::get($v,'name');
                    $field_rules = carr::get($v,'rules');
                    $field_value = carr::get($post,'custom_field_'.$k);
                    switch($field_type) {
                        case 'image':
                            
                            $resource = CResources::factory('image', 'custom-field-image-'.$k,$org_code);
                            $image_url = null;
                            $filename = null;
                            $file_name_generated = '';
                            if(isset($_FILES['custom_field_'.$k])&&$_FILES['custom_field_'.$k]!=null&&isset($_FILES['custom_field_'.$k]['tmp_name'])&&$_FILES['custom_field_'.$k]['tmp_name']!=null) {
                                $filename = $_FILES['custom_field_'.$k]['name'];
                                $content = file_get_contents($_FILES['custom_field_'.$k]['tmp_name']);
                                $file_name_generated = $resource->save($filename, $content);
                                $image_url = $resource->get_url($file_name_generated);
                            }
                            $field_value = array(
                                'filename'=>$filename,
                                'image_url'=>$image_url,
                            );
                            
                            if (strlen($id) == 0) {
                                if ($field_rules != null) {
                                    $image_required = carr::get($field_rules, 'required');
                                    if ($image_required) {
                                        if (strlen($filename) == 0) {
                                            $err_code++;
                                            $err_message = clang::__($field_name).' '.clang::__('is required');
                                        }
                                    }
                                }
                            }
                            
                        break;
                        default:
                            $field_value = carr::get($post,'custom_field_'.$k);
                        break;
                    }
                    $options = array();
                    $options['post_id']=$cms_post_id;
                    $options['field_name']=$field_name;
                    $options['field_value']=$field_value;
                    $options['field_type']=$field_type;
                    
                    ccms::update_custom_field($options);
                }
                
                
                //ccms::save_custom_field($cms_post_id, $post);
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
                } else {
                    cmsg::add("success", "Save Success");
                    curl::redirect(self::__CONTROLLER . "add");
                }
            }
        }
        

        

        $imgsrc = curl::base() . 'cresenity/noimage/120/120';
        if (strlen($item_image) > 0) {
            $imgsrc = $item_image;
        }

        // BUILD UI
        $form = $app->add_form()->set_enctype('multipart/form-data');

        $post_type_list = ccms::get_post_type_list();
        $div = $form->add_div()->add_class('row');


        $div_left = $div->add_div()->add_class('span8');
        $widget_left = $div_left->add_widget();
        $div_right = $div->add_div()->add_class('span4');
        $widget_right = $div_right->add_widget();

        // DIV LEFT
        $post_type_control = $widget_left->add_field()->set_label(clang::__("Post Type"))->add_control('post_type', 'select')->set_list($post_type_list)->set_value($post_type);
        $post_type_control->add_listener('ready')->add_handler('reload')->set_target('custom-field-container')
                ->set_url(curl::base() . 'cms/post/load_custom_field/' . $id)->add_param_input(array('post_type'));
        $post_type_control->add_listener('change')->add_handler('reload')->set_target('custom-field-container')
                ->set_url(curl::base() . 'cms/post/load_custom_field/' . $id)->add_param_input(array('post_type'));
        $post_type_control->add_listener('ready')->add_handler('reload')->set_target('template-container')
                ->set_url(curl::base() . 'cms/post/load_template/' . $id)->add_param_input(array('post_type'));
        $post_type_control->add_listener('change')->add_handler('reload')->set_target('template-container')
                ->set_url(curl::base() . 'cms/post/load_template/' . $id)->add_param_input(array('post_type'));
        $post_type_control->add_listener('ready')->add_handler('reload')->set_target('category-container')
                ->set_url(curl::base() . 'cms/post/load_category/' . $id)->add_param_input(array('post_type'));
        $post_type_control->add_listener('change')->add_handler('reload')->set_target('category-container')
                ->set_url(curl::base() . 'cms/post/load_category/' . $id)->add_param_input(array('post_type'));

        $widget_left->add_field()->set_label(clang::__("Post Title"))->add_control('post_title', 'text')->set_value($post_title);
        //$widget_left->add_field()->set_label(clang::__("Url"))->add_control('post_name', 'text')->set_value($post_name)->set_placeholder("Auto");


        $q_list_tag = "select distinct tag from cms_post_tag where 1=1 ";
        if (strlen($org_id) > 0) {
            $q_list_tag .=" and org_id=" . $db->escape($org_id);
        }
        $list_tag = cdbutils::get_array($q_list_tag);


        //$tag_control = $widget_left->add_field()->set_label(clang::__("Tag"))->add_control('post_tag', 'select-tag');
        //$tag_control->set_value($cms_tag_value)->set_list($list_tag);

        $widget_left->add_field()->set_label(clang::__("Post Content"))->add_control('post_content', 'ckeditor')->set_value($post_content);
        $widget_left->add_div('custom-field-container');
        // DIV RIGHT
        $widget_right->add_div('template-container');
        $widget_right->add_field()->set_label(clang::__("Post Status"))->add_control('post_status', 'select')->set_value($post_status)->set_list($status_list)->add_class('large');
        $widget_right->add_div('category-container');
        
        /*
        $widget_right->add_field()
                ->set_label(clang::__("Featured Image"))
                ->add_control('item_image', 'image')
                ->set_imgsrc($imgsrc)
                ->set_maxwidth(150)
                ->set_maxheight(100);
        */
        $form->add_action_list()->add_action()->set_label(clang::__("Submit"))->set_confirm(true)->set_submit(true);
        $form->set_validation(false);
        /*
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
         * 
         */
        echo $app->render();
    }

    
    public function load_template($id = "") {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $current_value = '';
        if(strlen($id)>0) {
            $row = cdbutils::get_row('select template from cms_post where cms_post_id='.$db->escape($id));
            if($row!=null) {
                $current_value = $row->template;
            }
        }
        $request = array_merge($_GET, $_POST);
        $post_type = carr::get($request, 'post_type');
        
        $options = array(
            'post_type'=>$post_type,
        );
        $template_list = ccms::get_template_list($options);
        $app->add_field()->set_label(clang::__("Template"))->add_control('template', 'select')->set_value($current_value)->set_list($template_list)->add_class('large');
        echo $app->render();
        
    }
    
    public function load_category($id = "") {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $current_value = '';
        if(strlen($id)>0) {
            $row = cdbutils::get_row('select cms_category_id from cms_post where cms_post_id='.$db->escape($id));
            if($row!=null) {
                $current_value = $row->cms_category_id;
            }
        }
        
        $request = array_merge($_GET, $_POST);
        $post_type = carr::get($request, 'post_type');
        $post_type_data = ccms::get_post_type_data($post_type);
        $category_type = carr::get($post_type_data,'category_type');
        
        
        
        $options = array(
            'category_type'=>$category_type,
        );
        $category_list = ccms::get_category_list($options);
        $app->add_field()->set_label(clang::__("Category"))->add_control('cms_category_id', 'select')->set_value($current_value)->set_list($category_list)->add_class('large');
        echo $app->render();
        
    }
            

    public function load_custom_field($id = "") {
        $app = CApp::instance();
        $request = array_merge($_GET, $_POST);
        $post_type = carr::get($request, 'post_type');
        $data = ccms::get_post_type_data($post_type);
        $custom_fields = carr::get($data, 'custom_fields');
        
        if($custom_fields==null) {
            $custom_fields = array();
        }
        $options = array(
            'container' => $app,
            'fields' => null,
            'custom_fields' => $custom_fields,
            'post_id' => $id,
            'prefix' => 'custom_field_',
        );
        ccms::generate_custom_field_input($options);



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

        if ($err_code == 0) {
            try {
                $db->begin();
                $db->update(
                        "cms_post", array("status" => 0, "updated" => date("Y-m-d H:i:s"), "updatedby" => $user->username), array("cms_post_id" => $id)
                );
                $q_relationships = "SELECT * FROM cms_term_relationships WHERE cms_post_id = " . $db->escape($id);
                $r_relationships = cdbutils::get_row($q_relationships);
                if ($r_relationships != NULL) {
                    $rel_id = $r_relationships->cms_term_relationships_id;
                    $db->update(
                            "cms_term_relationships", array("status" => 0, "updated" => date("Y-m-d H:i:s"), "updatedby" => $user->username), array("cms_term_relationships_id" => $rel_id)
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
