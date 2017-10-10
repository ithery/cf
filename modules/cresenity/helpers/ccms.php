<?php

/**
 * Description of cms
 *
 * @author Ecko Santoso
 * @since 09 Sep 15
 */
class ccms {

    static $all_post_type = null;
    static $all_options = null;
    static $all_theme = null;

    public static function get_template_data($options = null) {
        $theme = CF::theme();
        $theme_folder = '';
        if (strlen($theme) > 0 && $theme != 'default') {
            $theme_folder = $theme.'/';
        }
        
        $return = array();
        $file = CF::get_file('data', 'cms/'.$theme_folder.'template');
        if ($file == NULL) {
            $file = CF::get_file('data', 'cms/default/template');
            if ($file == NULL) {
                $file = CF::get_file('data', 'cms/template');
            }
        }
        if ($file != null) {
            $return = include $file;
        }
        return $return;
    }
    
    public static function get_category_type_data($options=null) {
        $theme = CF::theme();
        $theme_folder = '';
        if (strlen($theme) > 0 && $theme != 'default') {
            $theme_folder = $theme.'/';
        }
        
        $return = array();
        $file = CF::get_file('data', 'cms/'.$theme_folder.'category_type');
        if ($file == NULL) {
            $file = CF::get_file('data', 'cms/default/category_type');
            if ($file == NULL) {
                $file = CF::get_file('data', 'cms/category_type');
            }
        }
        if ($file != null) {
            $return = include $file;
        }
        return $return;
    }
    
    public static function get_category_type_list($options=null) {
        $data = self::get_category_type_data($options);
        $list = array();
        //$list['default'] = 'Default';
        
        foreach ($data as $k => $v) {
            
            $list[$k] = carr::get($v, 'name');
        }


        return $list;
    }
    
    public static function get_template_list($options=null) {
        $data = self::get_template_data($options);
        $list = array();
        //$list[''] = 'Default';
        $post_type = carr::get($options,'post_type');
        $templates = array();
        if(strlen($post_type)!=null) {
            $post_type_data = self::get_post_type_data($post_type);
            $templates = carr::get($post_type_data,'templates');
            if(!is_array($templates)) {
                $templates = array();
            }
        }
        foreach ($data as $k => $v) {
            if(count($templates)>0) {
                if(!in_array($k, $templates)) {
                    continue;
                }
            }
            $list[$k] = carr::get($v, 'name');
        }

        return $list;
    }

    public static function update_custom_field($options, $field_name = null, $field_value = null) {
        $db = CDatabase::instance();
        $set_null = false;
        //check custom_field
        $field_type = null;

        if (is_array($options)) {
            $post_id = carr::get($options, 'post_id');
            $field_name = carr::get($options, 'field_name');
            $field_value = carr::get($options, 'field_value');
            $field_type = carr::get($options, 'field_type');
        } else {
            $post_id = $options;
        }

        if (strlen($field_name) == 0) {
            throw new Exception('Field name is empty');
        }

        
        $r = null;
        $row = cdbutils::get_row('select * from cms_custom_field where cms_post_id=' . $db->escape($post_id) . " and field_name=" . $db->escape($field_name));
        if ($row !== null && strlen($field_type) == 0) {
            $field_type = $row->field_type;
        }
        $need_update = true;
        switch ($field_type) {
            case 'image':
                if(strlen(carr::get($field_value,'filename'))==0) {
                    $need_update = false;
                }
                $field_value = json_encode($field_value);
                break;
            case 'file':
                if(strlen(carr::get($field_value,'filename'))==0) {
                    $need_update = false;
                } else if (carr::get($field_value,'filename') === "clear_data"){
                    $need_update = true;
                    $set_null = true;
                }
                $field_value = json_encode($field_value);
                break;
        }
        $data = array(
            'field_name' => $field_name,
            'field_value' => $field_value,
        );
        if (($field_type != null) && strlen($field_type) > 0) {
            $data['field_type'] = $field_type;
        }
        if ($row === null) {
            $data['cms_post_id'] = $post_id;
            $r = $db->insert('cms_custom_field', $data);
        } else {
            if ($need_update) {
                if ($set_null) {
                    $data = array(
                        'field_name' => $field_name,
                        'field_value' => NULL,
                    );
                }
                $r = $db->update('cms_custom_field', $data, array('cms_post_id' => $post_id, 'field_name' => $field_name));
            }
        }
        return $r;
    }

    public static function get_custom_field($post_id, $field_name) {
        $db = CDatabase::instance();
        $row = cdbutils::get_row('select * from cms_custom_field where cms_post_id=' . $db->escape($post_id) . " and field_name=" . $db->escape($field_name));
        if ($row === null) {
            return null;
        }
        $field_value = $row->field_value;
        $field_type = $row->field_type;
        $value = $field_value;
        switch ($field_type) {
            case 'image':
                $value = json_decode($field_value, true);
                break;
            case 'file':
                $value = json_decode($field_value, true);
                break;
            default:
                $value = $field_value;
                break;
        }
        return $value;
    }

    public static function get_custom_fields($options) {
        $db = CDatabase::instance();
        $fields = array();
        $custom_fields = array();
        $post_id = carr::get($options, 'post_id');
        if ($post_id != null && strlen($post_id > 0)) {
            $post_type = cdbutils::get_value('select post_type from cms_post where cms_post_id=' . $db->escape($post_id));
            $data = ccms::get_post_type_data($post_type);
            $custom_fields = carr::get($data, 'custom_fields');
            if ($custom_fields == null) {
                $custom_fields = array();
            }
        }
        if (isset($options['custom_fields'])) {
            $custom_fields = carr::get($options, 'custom_fields');
        }
        if (count($custom_fields) > 0) {
            $custom_field_data = ccms::get_custom_field_data($custom_fields);
            $fields = carr::get($custom_field_data, 'fields');
        }
        if (isset($options['fields'])) {
            $fields = carr::get($options, 'fields');
        }

        return $fields;
    }

    public static function generate_custom_field_input($options) {

        $post_id = carr::get($options, 'post_id');
        $prefix = carr::get($options, 'prefix');
        $container = carr::get($options, 'container');
        $custom_fields = carr::get($options, 'custom_fields', array());
        
        if ($container === null) {
            $container = CApp::instance();
        }
        if ($prefix === null) {
            $prefix = '';
        }
        
        $static_fields = array();
        if (is_array($custom_fields) && count($custom_fields) > 0) {
            $get_fields = carr::get($custom_fields, 'fields', array());
            if (is_array($get_fields) && count($get_fields) > 0) {
                $static_fields = $get_fields;
            }
        }
        
        if (count($static_fields) > 0) {
            $fields = $static_fields;
        }
        else {
            $fields = self::get_custom_fields($options);
        }

        if (is_array($fields)) {
            foreach ($fields as $k => $field) {
                $label = carr::get($field, 'label');
                $type = carr::get($field, 'type');
                $default_value = carr::get($field, 'default_value');
                $info = carr::get($field, 'info');
                if (count($info) > 0) {
                    $row_container = $container->add_div()->add_class('row-fluid');
                    $left = $row_container->add_div()->add_class('span6');
                    $right = $row_container->add_div()->add_class('span6');
                    
                    $field = $left->add_field()->set_label($label);
                    $information = $right->add_div()->add_class('alert alert-info');
                    $information->add('<h4>'.clang::__('Information').'</h4>');
                    $ul = '<ul>';
                    foreach ($info as $info_k => $info_v) {
                        $ul .= '<li>'.$info_v.'</li>';
                    }
                    $ul .= '</ul>';
                    $information->add($ul);
                }
                else {
                    $field = $container->add_field()->set_label($label);
                }
                //create control

                if ($type == 'file') {
                    $field
                        ->add_div()
                        ->add_class('alert alert-info')
                        ->add('<h4>' . clang::__('Information') . '</h4>')
                        ->add('<ul>')
                        ->add('Maximum file size 2 MB')
                        ->add('</ul>');
                }

                $control = null;
                switch ($type) {
                    case 'image':
                        $control = $field->add_control($prefix . $k, $type);
                        break;
                    default:
                        $control = $field->add_control($prefix . $k, $type);
                        break;
                }
                if ($control == null) {
                    throw new Exception('Unknown control for ' . $k);
                }
                //set value
                switch ($type) {
                    case 'image':
                        $control->set_imgsrc(curl::base() . 'cresenity/noimage/100/100');
                        $control->set_maxwidth(100);
                        $control->set_maxheight(100);

                        if (strlen($post_id) > 0) {
                            $field_value = self::get_custom_field($post_id, $k);
                            $control->set_imgsrc(carr::get($field_value, 'image_url'));
                        }
                        break;
                    case 'file':
                        if (strlen($post_id) > 0) {
                            $field_value = self::get_custom_field($post_id, $k);
                            $control->set_value(carr::get($field_value, 'filename'));
                        }
                        break;    
                    default:
                        $control = $control->set_value($default_value);
                        if (strlen($post_id) > 0) {
                            $field_value = self::get_custom_field($post_id, $k);
                            $control = $control->set_value($field_value);
                        }
                        break;
                }
            }
        }
    }

    public static function get_custom_field_data($custom_fields) {
        $theme = CF::theme();
        $theme_folder = '';
        if (strlen($theme) > 0 && $theme != 'default') {
            $theme_folder = $theme.'/';
        }
        
        if (!is_array($custom_fields)) {
            $custom_fields = array($custom_fields);
        }
        $data = array();
        foreach ($custom_fields as $val) {
            $file = CF::get_file('data', 'cms/'.$theme_folder.'custom_field/' . $val);
            if ($file == NULL) {
                $file = CF::get_file('data', 'cms/default/custom_field/' . $val);
                if ($file == NULL) {
                    $file = CF::get_file('data', 'cms/custom_field/' . $val);
                }
            }
            $file_data = array();
            if (file_exists($file)) {
                $file_data = include $file;
            }
            $data = array_merge($file_data);
        }
        return $data;
    }

    public static function get_post_type_data($post_type) {
        $all_post_type = self::get_all_post_type();
        return carr::get($all_post_type, $post_type);
    }

    private static function get_all_post_type() {
        $theme = CF::theme();
        $theme_folder = '';
        if (strlen($theme) > 0 && $theme != 'default') {
            $theme_folder = $theme.'/';
        }
        
        if (self::$all_post_type === null) {
            $dir = CF::get_dir('data/cms/'.$theme_folder.'post_type');
            if ($dir == NULL) {
                $dir = CF::get_dir('data/cms/default/post_type');
                if ($dir == NULL) {
                    $dir = CF::get_dir('data/cms/post_type');
                }
            }
            $files = cfs::list_files($dir);
            if (is_array($files)) {
                foreach ($files as $f) {
                    $data = include $f;
                    $key = carr::get($data, 'name');
                    $value = carr::get($data, 'label');
                    $result[$key] = $data;
                }
            }
            self::$all_post_type = $result;
        }
        return self::$all_post_type;
    }

    public static function get_post_type_list() {
        $all_post_type = self::get_all_post_type();


        //$result = array('post' => 'Post');
        $result = array();
        foreach ($all_post_type as $k => $data) {
            $value = carr::get($data, 'label');
            $result[$k] = $value;
        }

        return $result;
    }
    
    public static function get_options_data($option) {
        $all_options = self::get_all_options();
        return carr::get($all_options, $option);
    }
    
    private static function get_all_options() {
        $theme = CF::theme();
        $theme_folder = '';
        if (strlen($theme) > 0 && $theme != 'default') {
            $theme_folder = $theme.'/';
        }
        
        if (self::$all_options === null) {
            $dir = CF::get_dir('data/cms/'.$theme_folder.'options');
            if ($dir == NULL) {
                $dir = CF::get_dir('data/cms/default/options');
                if ($dir == NULL) {
                    $dir = CF::get_dir('data/cms/options');
                }
            }
            $files = cfs::list_files($dir);

            if (is_array($files)) {
                foreach ($files as $f) {
                    $data = include $f;
                    $key = carr::get($data, 'name');
                    $value = carr::get($data, 'label');
                    $result[$key] = $data;
                }
            }
            self::$all_options = $result;
        }
        return self::$all_options;
    }
    
    public static function get_options_list() {
        $all_options = self::get_all_options();
        
        $result = array();
        foreach ($all_options as $k => $data) {
            $value = carr::get($data, 'label');
            $result[$k] = $value;
        }

        return $result;
    }
    
    private static function get_all_theme_list() {
        if (self::$all_theme === null) {
            $theme_file = CF::get_file('data','cms/theme');
            if (file_exists($theme_file)) {
                $theme_data = include $theme_file;
                $result = array();
                if (is_array($theme_data)) {
                    foreach ($theme_data as $theme_data_k => $theme_data_v) {
                        $result[$theme_data_k] = $theme_data_v;
                    }
                }
                self::$all_theme = $result;
            }
        }
        return self::$all_theme;
    }
    
    public static function get_theme_list() {
        $all_theme = self::get_all_theme_list();
        $result = array();
        foreach ($all_theme as $all_theme_k => $all_theme_v) {
            $value = carr::get($all_theme_v, 'label');
            $result[$all_theme_k] = $value;
        }

        return $result;
    }
    
    public static function get_theme_color($theme) {
        $all_theme_color = self::get_all_theme_list();
        return carr::get($all_theme_color, $theme);
    }

    public static function post_status() {
        return array(
            "publish" => "Publish",
            "draft" => "Draft",
        );
    }

    public static function category_list() {
        return self::__get_taxonomy('category');
    }
    
    public static function get_category_list($options=null) {
        $db = CDatabase::instance();

        $category_type=carr::get($options,'category_type');
        $org_id=carr::get($options,'org_id');
        if($category_type===null) $category_type = array();
        if(!is_array($category_type)) $category_type = array($category_type);
        $parent_id=carr::get($options,'parent_id');
        
        $result=array();
        if(count($category_type)==0) {
            $category_type[0]='default';
        }
        foreach($category_type as $cat_type) {
            $treedb = CTreeDB::factory('cms_category');
            if(strlen($cat_type)>0&&$cat_type!="ALL") {
                $treedb->add_filter('category_type', $cat_type);
            }
            if (strlen($org_id) > 0) {
                $treedb->set_org_id($org_id);
            }
            
            $list = $treedb->get_list("&nbsp;&nbsp;&nbsp;&nbsp;");
            $result = $result + $list;
        }
        
        //$result = array(''=>'NONE') + $result;
        
        return $result;
    }
    
    public static function get_category_list_child($options=null) {
        $db = CDatabase::instance();

        $category_type=carr::get($options,'category_type');
        if($category_type===null) $category_type = array();
        if(!is_array($category_type)) $category_type = array($category_type);
        $parent_id=carr::get($options,'parent_id');
        
        $result=array();
        if(count($category_type)==0) {
            $category_type[0]='default';
        }
        foreach($category_type as $cat_type) {
            $treedb = CTreeDB::factory('cms_category');
            if(strlen($cat_type)>0&&$cat_type!="ALL") {
                $treedb->add_filter('category_type', $cat_type);
            }
            
            $result = $treedb->get_children_data($parent_id);
            $cat_data = array();
            foreach ($result as $result_k => $result_v) {
                $arr_cat['category_id'] = carr::get($result_v, 'cms_category_id');
                $arr_cat['name'] = carr::get($result_v, 'name');
                $arr_cat['image_url'] = carr::get($result_v, 'image_url');
                $arr_cat['url_key'] = carr::get($result_v, 'url_key');
                $arr_cat['description'] = carr::get($result_v, 'description');
                $arr_cat['created'] = carr::get($result_v, 'created');
                $arr_cat['createdby'] = carr::get($result_v, 'createdby');
                $arr_cat['updated'] = carr::get($result_v, 'updated');
                $arr_cat['updatedby'] = carr::get($result_v, 'updatedby');
                $cat_data[] = $arr_cat;
            }
            $result = $cat_data;
        }
        return $result;
    }
    

    public static function category() {
        $db = CDatabase::instance();

        $result = array();
        $cat = self::__get_taxonomy('category');
        if (count($cat) > 0) {
            foreach ($cat as $cat_k => $cat_v) {
                $result[$cat_v['cms_terms_id']] = ucfirst($cat_v['name']);
            }
        }
        return $result;
    }

    public static function menu_group_list() {
        return self::__get_taxonomy('nav_menu');
    }

    public static function menu_group() {
        $result = array();
        $cat = self::__get_taxonomy('nav_menu');
        if (count($cat) > 0) {
            foreach ($cat as $cat_k => $cat_v) {
                $result[$cat_v['cms_terms_id']] = ucfirst($cat_v['name']);
            }
        }
        return $result;
    }

    private static function __get_taxonomy($type = 'category') {
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        $return = array();
        $q = $db->query("select
                            tr.*, tx.taxonomy
                            from cms_terms tr
                            join cms_term_taxonomy tx on tx.cms_terms_id = tr.cms_terms_id
                            where tr.status > 0
                            and tx.taxonomy = " . $db->escape($type) . "
                            and tx.`status` > 0 and tr.org_id = " . $org_id);
        if (count($q) > 0) {
            foreach ($q as $key => $value) {
                $arr_return['cms_terms_id'] = $value->cms_terms_id;
                $arr_return['name'] = $value->name;
                $arr_return['slug'] = $value->slug;
                $arr_return['taxonomy'] = $value->taxonomy;
                $arr_return['created'] = $value->created;
                $arr_return['createdby'] = $value->createdby;
                $return[] = $arr_return;
            }
        }
        return $return;
    }

    public static function get_post($id = '') {

        $result = self::__get_post_single($id);

        return $result;
    }

    public static function get_posts($options) {

        $result = self::__get_post($options);
        return $result;
    }

    public static function post($id = '') {
        $result = self::__get_post('post');
        if (strlen($id) > 0 && is_numeric($id)) {
            $result = self::__get_post_single("post", $id);
        }
        return $result;
    }

    public static function page($id = '') {
        $result = self::__get_post_single($id);
        return $result;
    }

    public static function page_select($org_id = NULL) {
        if ($org_id == null) {
            $org_id = CF::org_id();
        }

        $result = array();
        $cat = self::__get_post(array('post_type'=>'page'), $org_id);
        if (count($cat) > 0) {
            foreach ($cat as $cat_k => $cat_v) {
                $result[$cat_v['cms_post_id']] = ucfirst($cat_v['post_title']);
            }
        }
        return $result;
    }

    private static function __get_post($options, $org_id = null) {
        $db = CDatabase::instance();
        $result = array();
        if ($org_id == null) {
            $org_id = CF::org_id();
        }
        $post_type = carr::get($options,'post_type');
        $category_id = carr::get($options,'category_id');
        $sortby = carr::get($options,'sortby');
        $custom_field = carr::get($options,'custom_field', array());
        
        $q_custom_field = "";
        if (count($custom_field) > 0) {
            foreach ($custom_field as $custom_field_k => $custom_field_v) {
                $q_custom_field .= ", (SELECT field_value FROM cms_custom_field WHERE cms_post_id=p.cms_post_id AND field_name=".$db->escape($custom_field_v)." LIMIT 1) ".$custom_field_v;
            }
        }
        
        $q = "SELECT
                tr.cms_term_taxonomy_id, tt.cms_terms_id, t.name term_name, t.slug term_slug,
                p.*
                ".$q_custom_field."
               FROM cms_post p
               LEFT JOIN cms_term_relationships tr ON tr.cms_post_id=p.cms_post_id
               LEFT JOIN cms_term_taxonomy tt ON tt.cms_term_taxonomy_id = tr.cms_term_taxonomy_id
               LEFT JOIN cms_terms t ON t.cms_terms_id=tt.cms_terms_id
               WHERE p.status > 0";
        if ($post_type != null && strlen($post_type) > 0) {
            $q.=" AND p.post_type = " . $db->escape($post_type);
        }
        if($category_id!=null&&strlen($category_id)>0) {
               $q.=" AND p.cms_category_id = " . $db->escape($category_id);
        }
        if ($org_id != null) {
            $q.= " AND p.org_id=" . $db->escape($org_id);
        }
        
        if(strlen($sortby)>0) {
            if($sortby=='created_asc') {
                $q.=" order by p.created asc, updated asc";
            }
            if($sortby=='created_desc') {
                $q.=" order by p.created desc, updated desc";
            }
        }
        
        $r = $db->query($q);
        if (count($r) > 0) {
            foreach ($r as $r_k => $r_v) {
                $arr_result['cms_term_taxonomy_id'] = $r_v->cms_term_taxonomy_id;
                $arr_result['cms_terms_id'] = $r_v->cms_terms_id;
                $arr_result['term_name'] = $r_v->term_name;
                $arr_result['term_slug'] = $r_v->term_slug;
                $arr_result['cms_post_id'] = $r_v->cms_post_id;
                $arr_result['post_content'] = $r_v->post_content;
                $arr_result['post_title'] = $r_v->post_title;
                $arr_result['post_excerpt'] = $r_v->post_excerpt;
                $arr_result['post_status'] = $r_v->post_status;
                $arr_result['post_name'] = $r_v->post_name;
                $arr_result['post_parent'] = $r_v->post_parent;
                $arr_result['post_feature_image'] = $r_v->url_location;
                $arr_result['guid'] = $r_v->guid;
                $arr_result['menu_order'] = $r_v->menu_order;
                $arr_result['post_type'] = $r_v->post_type;
                $arr_result['post_mime_type'] = $r_v->post_mime_type;
                $arr_result['created'] = $r_v->created;
                $arr_result['createdby'] = $r_v->createdby;
                if (count($custom_field) > 0) {
                    foreach ($custom_field as $custom_field_k => $custom_field_v) {
                        $arr_result[$custom_field_v] = cobj::get($r_v, $custom_field_v);
                    }
                }
                $result[] = $arr_result;
            }
        }
        
        return $result;
    }

    private static function __get_post_single($id) {
        $db = CDatabase::instance();
        $result = array();

        $q = "SELECT *
               FROM cms_post p
               WHERE p.status > 0
               AND  p.cms_post_id=" . $db->escape($id);
        $r = cdbutils::get_row($q);
        return $r;
    }

    public static function add_slash_domain($domain) {
        $last_domain = substr($domain, -1, 1);
        if ($last_domain != '/') {
            $domain .= '/';
        }
        return $domain;
    }

    /**
     * @param string/int $menu_name can be menu_name or menu_id
     */
//    public static function nav_menu($menu_name = '') {
//        $db = CDatabase::instance();
//        
//        $return = array();
//        if (strlen($menu_name) > 0) {
//            if (is_numeric($menu_name)) {
//                $get_cms_term_id = cdbutils::get_row("SELECT * FROM cms_terms WHERE cms_terms_id = ".$db->escape($menu_name)." AND STATUS > 0");
//            }
//            else {
//                $get_cms_term_id = cdbutils::get_row("SELECT * FROM cms_terms WHERE slug = ".$db->escape($menu_name)." AND STATUS > 0");
//            }
//            if ($get_cms_term_id != NULL) {
//                $cms_terms_id = $get_cms_term_id->cms_terms_id;
//                
//                $q_menu = "SELECT * FROM cms_term_relationships WHERE cms_term_taxonomy_id = ".$db->escape($cms_terms_id) ." AND status > 0 ORDER BY term_order ASC";
//                $menu_item = $db->query($q_menu);
//                if (count($menu_item) > 0) {
//                    foreach ($menu_item as $menu_item_k => $menu_item_v) {
//                        $cms_post_id = $menu_item_v->cms_post_id;
//                        $arr_return['cms_term_relationships_id'] = $menu_item_v->cms_term_relationships_id;
//                        $arr_return['cms_term_taxonomy_id'] = $menu_item_v->cms_term_taxonomy_id;
//                        $arr_return['cms_post_id'] = $cms_post_id;
//                        $arr_return['term_order'] = $menu_item_v->term_order;
//                        
//                        // GET TITLE MENU from cms_post
//                        $q_post = "SELECT * FROM cms_post WHERE cms_post_id = ".$db->escape($cms_post_id)." AND post_type = 'nav_menu_item'";
//                        $post = cdbutils::get_row($q_post);
//                        $menu_name = '';
//                        if ($post != NULL) {
//                            $menu_name = $post->post_title;
//                        }
//                        
//                        // GET DETAIL MENU FROM postmeta
//                        $q_meta = "SELECT * FROM cms_postmeta WHERE cms_post_id =".$db->escape($cms_post_id);
//                        $postmeta = $db->query($q_meta);
//                        $menu_type = '';
//                        $menu_url = '';
//                        $menu_icon = '';
//                        $taxonomy_id = '';
//                        if (count($postmeta) > 0) {
//                            foreach ($postmeta as $postmeta_k => $postmeta_v) {
////                                cdbg::var_dump($postmeta_v);
//                                $meta_key = $postmeta_v->meta_key;
//                                $meta_value = $postmeta_v->meta_value;
//                                if ($meta_key == '_menu_item_object') {
//                                    $menu_type = $meta_value;
//                                }
//                                if ($meta_key == '_menu_item_url' && strlen($meta_value) > 0) {
//                                    $menu_url = $meta_value;
//                                }
//                                if ($meta_key == '_menu_item_object_id') {
//                                    $taxonomy_id = $meta_value;
//                                }
//                                if ($meta_key == '_menu_item_classes') {
//                                    $menu_icon = $meta_value;
//                                }
//                            }
//                        }
//                        
//                        if ($menu_type == 'category') {
//                            // GET TERMS
//                            if (strlen($taxonomy_id) > 0) {
//                                $get_term = cdbutils::get_row("SELECT * FROM cms_terms WHERE cms_terms_id = ".$db->escape($taxonomy_id));
//                                if ($get_term != NULL) {
//                                    $menu_url = curl::base(). 'read/'.$menu_type. '/'.$get_term->slug;
//                                }
//                            }
//                        }
//                        
//                        if ($menu_type == 'page') {
//                            if (strlen($taxonomy_id) > 0) {
//                                $get_page = cdbutils::get_row("SELECT * FROM cms_post WHERE cms_post_id = ".$db->escape($taxonomy_id));
//                                if ($get_page != NULL) {
//                                    $menu_url = curl::base() .'read/'.$menu_type. '/'. $get_page->post_name;
//                                }
//                            }
//                        }
//                        
//                        $arr_return['menu_type'] = $menu_type;
//                        $arr_return['menu_name'] = $menu_name;
//                        $arr_return['menu_url'] = $menu_url;
//                        $arr_return['menu_icon'] = $menu_icon;
//                        
//                        $return[] = $arr_return;
//                    }
//                }
//            }
//            
//        }
//        else {
//            // DO VIEW ALL MENU
//        }
//        
//        return $return;
//    }

    public static function menu_attr() {
        $return = array(
            "_menu_item_type" => "",
            "_menu_item_menu_item_parent" => "",
            "_menu_item_object_id" => "",
            "_menu_item_object" => "",
            "_menu_item_target" => "",
            "_menu_item_classes" => "",
            "_menu_item_url" => "",
        );
        return $return;
    }

    public static function excerpt_paragraph($html, $max_char = 100, $trail = '...') {
        // temp var to capture the p tag(s)
        $matches = array();
        if (preg_match('/<p>[^>]+<\/p>/', $html, $matches)) {
            // found <p></p>
            $p = strip_tags($matches[0]);
        } else {
            $p = strip_tags($html);
        }
        //shorten without cutting words
        $p = self::__short_str($p, $max_char);

        // remove trailing comma, full stop, colon, semicolon, 'a', 'A', space
        $p = rtrim($p, ',.;: aA');

        // return nothing if just spaces or too short
        if (ctype_space($p) || $p == '' || strlen($p) < 10) {
            return '';
        }

        return '<p>' . $p . $trail . '</p>';
    }

    private function __short_str($str, $len, $cut = false) {
        if (strlen($str) <= $len) {
            return $str;
        }
        $string = ( $cut ? substr($str, 0, $len) : substr($str, 0, strrpos(substr($str, 0, $len), ' ')) );
        return $string;
    }

    // NEW MENU CONCEPT

    /**
     * @param string/int $menu_name can be menu_name or menu_id
     */
    public static function nav_menu($menu_name = '') {
        $db = CDatabase::instance();
        $org_id = CF::org_id();

        $all_menu = array();

        if (strlen($menu_name) > 0) {
            $cms_terms_id = NULL;
            if (is_numeric($menu_name)) {
                $cms_terms_id = $menu_name;
            } else {
                // Get cms_terms_id from cms_terms (slug)
//                $get_cms_term_id = cdbutils::get_row("SELECT * FROM cms_terms WHERE slug = " . $db->escape($menu_name) . " AND STATUS > 0");
                $get_cms_term_id = cdbutils::get_row("SELECT * FROM cms_terms WHERE slug = " . $db->escape($menu_name) . " AND STATUS > 0 AND org_id = ".$db->escape($org_id));
                if ($get_cms_term_id != NULL) {
                    $cms_terms_id = $get_cms_term_id->cms_terms_id;
                }
            }

            if ($cms_terms_id != NULL) {
//                $q_menu = "SELECT * FROM `cms_menu` WHERE STATUS>0 AND `cms_terms_id` = " . $db->escape($cms_terms_id) . " ORDER BY lft ASC";
                $q_menu = "SELECT * FROM `cms_menu` WHERE STATUS>0 AND `cms_terms_id` = " . $db->escape($cms_terms_id) . " AND org_id = ".$db->escape($org_id)." ORDER BY lft ASC";
                $menu_res = $db->query($q_menu);
                if (count($menu_res) > 0) {
                    foreach ($menu_res as $menu_res_k => $menu_res_v) {
                        $menu_type = $menu_res_v->menu_item_type;
                        $menu_object_id = $menu_res_v->menu_item_object_id;
                        $menu_object = $menu_res_v->menu_item_object;
                        $menu_target = $menu_res_v->menu_item_target;
//                        cdbg::var_dump($menu_type);
                        $menu_url = $menu_res_v->menu_item_url;
                        $menu_tail = '';
                        if ($menu_object == 'category') {
                            // GET TERMS
                            if (strlen($menu_object_id) > 0) {
                                $get_term = cdbutils::get_row("SELECT * FROM cms_category WHERE cms_category_id = " . $db->escape($menu_object_id) ." AND org_id=".$db->escape($org_id));
                                if ($get_term != NULL) {
                                    $menu_tail = $get_term->url_key;
                                }
                            }
                        }

                        if ($menu_object == 'page') {
                            if (strlen($menu_object_id) > 0) {
//                                $get_page = cdbutils::get_row("SELECT * FROM cms_post WHERE cms_post_id = " . $db->escape($menu_object_id));
                                $get_page = cdbutils::get_row("SELECT * FROM cms_post WHERE cms_post_id = " . $db->escape($menu_object_id) . " AND org_id = ".$db->escape($org_id));
                                if ($get_page != NULL) {
                                    $menu_tail = $get_page->post_name;
                                }
                            }
                        }

                        if ($menu_object != 'custom') {
                            $menu_url = curl::base() . 'read/' . $menu_object . '/' . $menu_tail;
                        }

                        $arr_menu['cms_menu_id'] = $menu_res_v->cms_menu_id;
                        $arr_menu['parent_id'] = $menu_res_v->parent_id;
                        $arr_menu['depth'] = $menu_res_v->depth;
                        $arr_menu['menu_name'] = $menu_res_v->name;
                        $arr_menu['menu_type'] = $menu_object;
                        $arr_menu['menu_url'] = $menu_url;
                        $arr_menu['menu_icon'] = $menu_res_v->menu_item_classes;

                        $all_menu[] = $arr_menu;
                    }
                }
            }
        }

        $return = self::__parseTree($all_menu);
        return $return;
    }
    
    public static function get_permalink($post_id) {
        $db = CDatabase::instance();
        $org_id = CF::org_id();
//        $post_name = cdbutils::get_value('select post_name from cms_post where cms_post_id='.$db->escape($post_id));
        $post_name = cdbutils::get_value('select post_name from cms_post where cms_post_id='.$db->escape($post_id) ." AND org_id = ".$db->escape($org_id));
        if($post_name!==null) {
            $menu_url = curl::base() . 'read/post/' . $post_name;
            return $menu_url;
        }
        return null;
        
    }
    public static function get_category_link($category_id) {
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        $url_key = cdbutils::get_value('select url_key from cms_category where cms_category_id='.$db->escape($category_id) ." AND org_id = ".$db->escape($org_id));
        if($url_key!==null) {
            $menu_url = curl::base() . 'read/category/' . $url_key;
            return $menu_url;
        }
        return null;
        
    }
    
    public static function get_category($category_id) {
        $db = CDatabase::instance();
        $row = cdbutils::get_row('select * from cms_category where cms_category_id='.$db->escape($category_id));
        return $row;
        
    }

    private static function __parseTree($tree, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $tree_k => $tree_v) {
            # A direct child is found
            $cms_menu_id = $tree_v['cms_menu_id'];
            $parent = $tree_v['parent_id'];
            $menu_name = $tree_v['menu_name'];
            $menu_type = $tree_v['menu_type'];
            $menu_url = $tree_v['menu_url'];
            $menu_icon = $tree_v['menu_icon'];

            if ($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                //unset($tree[$cms_menu_id]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'menu_id' => $cms_menu_id,
                    'menu_name' => $menu_name,
                    'menu_type' => $menu_type,
                    'menu_url' => $menu_url,
                    'menu_icon' => $menu_icon,
                    'sub_menu' => self::__parseTree($tree, $cms_menu_id)
                );
            }
        }
        return empty($return) ? null : $return;
    }

    public static function get_option($param, $view = "single") {
        $db = CDatabase::instance();
        $org_id = CF::org_id();
        $result = '';
        if ($view == 'list') {
            $result = array();
        }
        $param_allowed = array(
            "site_url",
            "home",
            "blog_name",
            "blogdescription",
            "admin_email",
            "start_of_week",
            "posts_per_page",
            "date_format",
            "time_format",
            "google_analytic",
            "contact_us",
            "contact_us_phone_1",
            "footer",
            "media",
            "info",
            "info_title",
            "info_subtitle",
            "utilities_retrieve",
//            "color_scheme",
//            "theme_scheme",
        );
        if (in_array($param, $param_allowed) == TRUE) {
            if ($view == "single") {
                $row = cdbutils::get_row("SELECT * FROM cms_options WHERE option_name = " . $db->escape($param) . " and org_id = " . $org_id);
                if ($row != NULL) {
                    $result = $row->option_value;
                }
            }
            if ($view == "list") {
                $res = $db->query("SELECT * FROM cms_options WHERE option_name LIKE '{$param}%' and org_id = " . $org_id);

                switch ($param) {
                    case 'contact_us' :
                        foreach ($res as $res_k => $res_v) {
                            $name = $res_v->option_name;
                            $value = $res_v->option_value;
                            $class_name = preg_replace('#contact_us_|_1|_2#ims', "", $name);
                            $class_name = preg_replace('#wa#ims', "whatsapp", $class_name);
                            $class_name = preg_replace('#mail#ims', "envelope", $class_name);
                            $class_name = preg_replace('#bbm#ims', "mobile", $class_name);
                            $class_name = "fa-" . $class_name;
                            if ($name != 'contact_us') {
                                if ($value != NULL || strlen($value) > 0) {
                                    $arr_result['name'] = $name;
                                    $arr_result['value'] = $value;
                                    $arr_result['icon'] = $class_name;
                                    $result[] = $arr_result;
                                }
                            }
                        }
                        break;
                    case 'media':
                        $label = array(
                            "media_fb" => "Facebook",
                            "media_tw" => "Twitter",
                            "media_gp" => "Google+",
                            "media_yt" => "Youtube",
                            "media_is" => "Instagram",
                        );
                        foreach ($res as $res_k => $res_v) {
                            $name = $res_v->option_name;
                            $value = $res_v->option_value;
                            $class_name = preg_replace('#media_#', "", $name);
                            $temp_class = $class_name;
                            $class_name = preg_replace('#fb#ims', "facebook", $class_name);
                            $class_name = preg_replace('#tw#ims', "twitter", $class_name);
                            $class_name = preg_replace('#gp#ims', "google-plus", $class_name);
                            $class_name = preg_replace('#yt#ims', "youtube", $class_name);
                            $class_name = preg_replace('#is#ims', "instagram", $class_name);
                            $class_name = "fa-" . $class_name;
                            if ($name != 'contact_us') {
                                if ($value != NULL || strlen($value) > 0) {
                                    $arr_result['name'] = $name;
                                    $arr_result['label'] = $label[$name];
                                    $arr_result['value'] = $value;
                                    $arr_result['icon'] = $class_name;
                                    $arr_result['class'] = $temp_class;
                                    $result[] = $arr_result;
                                }
                            }
                        }
                        break;
                    default :
                        foreach ($res as $res_k => $res_v) {
                            $name = $res_v->option_name;
                            $value = $res_v->option_value;
                            $arr_result['name'] = $name;
                            $arr_result['value'] = $value;
                            $result[] = $arr_result;
                        }
                        break;
                }
            }
        }

        return $result;
    }
    
    

}
