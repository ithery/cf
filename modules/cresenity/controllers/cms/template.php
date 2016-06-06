<?php

    /**
     *
     * @author Seians0077
     * @since  Dec 7, 2015
     * @license http://piposystem.com Piposystem
     */
    class Template_Controller extends CController {
        
        protected $_controller='cms/template/';
        
        public function __construct() {
            parent::__construct();
        }

        public function index() {
            $app = CApp::instance();
            $user = $app->user();
            $widget = $app->add_widget();
            $header_action_add = $widget->set_title('Template')->add_header_action('add_template')->set_label('ADD')->set_icon('plus')->add_class('btn-success')->set_link(curl::base() . $this->_controller . 'add');
            echo $app->render();
        }

        public function add() {
            $this->edit();
        }

        public function edit($id = '') {
            $app = CApp::instance();
            //variable
            $header_name='';
            $header_file='';
            $header_label='';
            $custom_fields_count=0;
            
            //submit
            $post=$_POST;
            if(!$post==null){
                $file = CF::get_file('data', 'template_setting');

                $file_template_setting = array();
                if (file_exists($file)) {
                    $file_template_setting = include $file;
                }

                $key = carr::get($post, 'header_name');
                $file_template_setting[$key] = $post;
                $path = CF::get_dir('data') . 'template_setting.php';
                cphp::save_value($file_template_setting, $path);
                
                
            }
            //UI
            $form=$app->add_form();
            $content=$form->add_div('content');
            //header
            $widget_header=$content->add_widget()->set_title('Header');
            $widget_header->add_field()->set_label('Name')->add_control('header_name','text')->set_value($header_name);
            $widget_header->add_field()->set_label('File')->add_control('header_file','text')->set_value($header_file);
            $widget_header->add_field()->set_label('Label')->add_control('header_label','text')->set_value($header_label);
            
            //custom field
            $widget_custom_field=$content->add_widget()->set_title('Custom Field');
            $widget_custom_field->add_div('custom_field_content');
            $header_action_custom_field_add=$widget_custom_field->add_header_action('add_custom_field')->set_label('ADD')->set_icon('plus')->add_class('btn-success')->set_attr('count',0)->set_attr('rel','');
            $listener_header_action_custom_field_add = $header_action_custom_field_add->add_listener('click');
            $listener_header_action_custom_field_add
                    ->add_handler('custom')
                    ->set_js("add_custom_field()");
            
            $div_action=$form->add_div();
            $div_action->add_action_list()->add_action()->set_label(clang::__('Submit'))->set_submit(true)->set_confirm(true);
            $js="
                function add_custom_field(){
                    var obj_count=$('#add_custom_field');
                    var count=obj_count.attr('count');
                    var rel=obj_count.attr('rel');
                    count++;
                    $('#add_custom_field').attr('count',count);    
                    $.cresenity.append('custom_field_content','" . curl::base() . $this->_controller."add_custom_field','get',{'count':count,'rel':rel});                }
                    
            ";
            $app->add_js($js);        
            echo $app->render();
        }

        public function add_custom_field(){
            $app=CApp::instance();
            $get=$_GET;
            $count=carr::get($get,'count');
            $rel=carr::get($get,'rel');
            $custom_field='custom_field';
            if(strlen($rel)>0){
                $arr_rel=explode('_',$rel);             
                foreach($arr_rel as $val){
                    $custom_field.='['.$val.']';
                }
                $rel.='_'.$count.'_data';
            }else{
                $rel.=$count.'_data';
            }
            $custom_field.='['.$count.']';
            
            
            $widget_content=$app->add_widget();
            $widget_content->add_field()->set_label('Name')->add_control('','text')->set_name($custom_field.'[name]');
            $widget_content->add_field()->set_label('Label')->add_control('','text')->set_name($custom_field.'[label]');
            $widget_content->add_field()->set_label('Type')->add_control('','text')->set_name($custom_field.'[type]');
            $widget_content->add_field()->set_label('Default Value')->add_control('','text')->set_name($custom_field.'[default_value]');
            //validation not yet
            $widget_content_custom_field=$widget_content->add_widget()->set_title('Custom Field');
            $widget_content_custom_field->add_div('custom_field_content_'.$rel.'_'.$count);
            $header_action_custom_field_add=$widget_content_custom_field->add_header_action('add_custom_field_'.$rel.'_'.$count)->set_label('ADD')->set_icon('plus')->add_class('btn-success')->set_attr('count',0)->set_attr('rel',$rel);
            $listener_header_action_custom_field_add = $header_action_custom_field_add->add_listener('click');
            $listener_header_action_custom_field_add
                    ->add_handler('custom')
                    ->set_js("add_custom_field_".$rel."_".$count."()");
            
            
            $js="
                function add_custom_field_".$rel."_".$count."(){
                    var obj_count=$('#add_custom_field_".$rel."_".$count."');
                    var count=obj_count.attr('count');
                    var rel=obj_count.attr('rel');
                    count++;   
                    $('#add_custom_field_".$rel."_".$count."').attr('count',count);    
                    $.cresenity.append('custom_field_content_".$rel."_".$count."','" . curl::base() . $this->_controller."add_custom_field','get',{'count':count,'rel':rel});                }
                    
            ";
            $app->add_js($js);        
            
            echo $app->render();
        }
    }
    