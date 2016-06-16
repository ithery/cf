<?php

/**
 * Description of read
 *
 * @author Ecko Santoso
 * @since 23 Sep 15
 */
class Read_Controller extends CController {
    
    public function __construct() {
        parent::__construct();
    }
    
//    public function index($category = '', $post = '') {
//        $app = CApp::instance();
//        
//        $app->add($category.'-'.$post);
//        
//        $app->add("OKE");
//        echo $app->render();
//    }
    
    // Reading category
    public function category($category = '', $post='') {
//        $app = CApp::instance();
        $title = '';
        $request = $_GET;
        $page = carr::get($request, 'page');
        
        $the_post = cms::get_post('', $category, $page);
        
        if (strlen($post) > 0) {
            $the_post = cms::get_post($post, $category);
            $this->view_post($the_post);
            return FALSE;
        }
        
//        cdbg::var_dump(count($the_post));
//        cdbg::var_dump($the_post);
        
        $this->view_category($category, $the_post);
//        cdbg::var_dump($the_post);
//        echo $app->render();
    }

    // Reading page
    public function page($post = '') {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $post_title = '';
        $post_content = '';
        $post_id = $post;
        if (!is_numeric($post_id)) {
            $post_id = cdbutils::get_value("SELECT cms_post_id FROM cms_post WHERE post_name = ".$db->escape($post_id));
        }
        $post = ccms::get_post($post_id);
        $template = $post->template;
        if(strlen($template)>0) {
            $template = 'cms/'.$template;
        } else {
            $template = 'cms/post';

        }
        $file = CF::get_file('template',$template);
        if($file===null) {
            $template = 'cms/index';
        }
        
        $file = CF::get_file('template',$template);
        if($file===null) {
            $template = 'cms/404';
        }
        $file = CF::get_file('template',$template);
        if($file===null) {
            CF::show_404();
            return;
        } else {
            
            $app->show_breadcrumb(false);
            $app->show_title(false);
            include $file;
        }
        echo $app->render();
    }
    
    public function view_category($category, $the_post) {
        $app = CApp::instance();
//        $app->add("CATEGORY");
        
        $category_name = ucfirst($category);
        $title_area = $app->add_div()->add_class("row-fluid");
        $title_area->add('<h1 class="heading">'.$category_name.'</h1>');
        
        $content_area = $app->add_div()->add_class("row-fluid");
        
        if (count($the_post) > 0) {
            foreach ($the_post as $the_post_k => $the_post_v) {
                $title = $the_post_v['post_title'];
                $content = $the_post_v['post_content'];
                $excerpt = $the_post_v['post_excerpt'];
                $permalink = $the_post_v['post_permalink'];
                $date = $the_post_v['post_date'];
                $time = $the_post_v['post_time'];
                $author = $the_post_v['post_author'];
                $thumbnail = $the_post_v['post_thumbnail'];
                
                $content = $content_area->add_div()->add_class("row box-list");
                $content_list = $content->add_div()->add_class("col-md-12");
                
                $content_inner = $content_list->add_div();
                $content_sub_inner_1 = $content_inner->add_div()->add_class("box-separate");
                $content_sub_inner_top = $content_sub_inner_1->add_div()->add_class("row box-content");
                $content_sub_inner_top_left = $content_sub_inner_top->add_div()->add_class("col-md-2");
                $content_sub_inner_top_right = $content_sub_inner_top->add_div()->add_class("col-md-10");
                
                $content_sub_inner_top_left->add('<img src="'.$thumbnail.'" alt="Loading" class="img-responsive"/>');
                
                $post_title = $content_sub_inner_top_right->add('<h2 class="post-title"><a href="'.$permalink.'" title="'.$title.'">'.$title.'</a></h2>');
                $post_excerpt = $content_sub_inner_top_right->add($excerpt);
                $post_excerpt = $content_sub_inner_top_right->add('<a href="'.$permalink.'" title="'.clang::__("Read more of "). $title.'">'.clang::__("Read More").'</a>');
                
                $content_sub_inner_2 = $content_inner->add_div()->add_class("box-author");
                $post_author = '<span class="post-author">'.$date.' At '.$time.' | '.clang::__("By").' '.$author.'</span>';
                $content_sub_inner_2->add($post_author);
//                $content_list->add($excerpt);
            }
        }
        
//        $content_area->add($the_post);
        
        echo $app->render();
    }
    
    public function view_post($the_post) {
        $app = CApp::instance();
        if ($the_post != NULL) {
            $title = $the_post['post_title'];
            $content = $the_post['post_content'];
            $date = $the_post['post_date'];
            $time = $the_post['post_time'];
            $author = $the_post['post_author'];
            $category = $the_post['term_name'];
            $image = $the_post['post_thumbnail'];
            
            $content_area = $app->add_div()->add_class("row-fluid");
//            if (strlen($image) > 0) {
//                $image_div = $content_area->add_div();
//                $image_div->add('<img src="'.$image.'" alt="Loading"/>');
//            }
            
            $title_div = $content_area->add_div();
            $title_div->add('<h1 class="post-title underline">'.$title.'</h1>');
            $content_div = $content_area->add_div();
            $content_div->add('<article style="word-wrap: break-word">'.$content.'</article>');
        }
        
//        $app->add($the_post);
        
        echo $app->render();
    }
}
