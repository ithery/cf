<?php

    /**
     *
     * @author Seians0077
     * @since  Dec 7, 2015
     * @license http://piposystem.com Piposystem
     */
    class Post_Controller extends CController {
        
        protected $_controller='cms/post/';
        protected static $list_template_setting=array();
        
        
        public function __construct() {
            parent::__construct();
            $file_themes_setting = CF::get_file('data', 'template_setting');
            if(file_exists($file_themes_setting)){
                $this->list_template_setting = include $file_themes_setting; 
            }
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
            $template='';
            //$custom_fields_count=0;
            
           
            //submit
            $post=$_POST;
            
          
            
            if(!$post==null){
            }
            //UI
            foreach($this->list_template_setting as $key=>$val){    
                $arr_template[$key]= carr::get($val,'header_label');
            }
            
            $form=$app->add_form();
            $content=$form->add_div('content');
            
            //header
            $widget_header=$content->add_widget();
            $template_control = $widget_header->add_field()->set_label(clang::__('Template'))->add_control('template', 'select')
                            ->set_value($template)
                            ->set_list($arr_template);
            $listener = $template_control->add_listener('change')
                        ->add_handler('reload')
                        ->add_param_input(array('template'))
                        ->set_target('div_custom_field')
                        ->set_url(curl::base().$this->_controller.'custom_field');

            $listener = $template_control->add_listener('ready')
                        ->add_handler('reload')
                        ->add_param_input(array('template'))
                        ->set_target('div_custom_field')
                        ->set_url(curl::base().$this->_controller.'custom_field');
            
            //custom field
            $widget_cutom_field=$content->add_widget();        
            $div_custom_field=$widget_cutom_field->add_div('div_custom_field');
            
            echo $app->render();
        }
        
        public function custom_field(){
            $app=CApp::instance();
            $get=$_GET;
            
            $template=carr::get($get,'template');
            if(!$template==null){
                $data_template=carr::get($this->list_template_setting,$template);
            }else{
                $rel=carr::get($get,'rel');
            }
            
            
            $app->add($data_template);
            $custom_field=carr::get($data_template,'custom_field',array());
            if(is_array($custom_field)){
                $this->generate_custom_field($custom_field, $app);
            }
            
            
            echo $app->render();
        }
                
        
        public function generate_custom_field($data,$obj,$rel){
            //$new_obj=  CFactory::create_div();
            $app=CApp::instance();
            foreach($data as $key=>$val){
                $type=carr::get($val,'type');
                $label=carr::get($val,'label');
                $name=carr::get($val,'name');
                $default_value=carr::get($val,'default_value');
                switch($type){
                    case 'repeater' : {
                        $repeater_data=carr::get($val,'data');
                        $widget=$obj->add_widget()->set_title(clang::__($label))->add_header_action()->set_label('ADD')->set_icon('plus')->add_class('btn-success')->set_attr('count',0)->set_attr('rel','');;
                        $widget->add_div('div_'.$name);
                        $listener_header_action_custom_field_add = $header_action_custom_field_add->add_listener('click');
                        $listener_header_action_custom_field_add
                                ->add_handler('custom')
                                ->set_js("add_repeater()");
                        $js="
                            function add_repeater(){
                                var obj_count=$('#add_custom_field');
                                var count=obj_count.attr('count');
                                var rel=obj_count.attr('rel');
                                count++;
                                $('#add_custom_field').attr('count',count);    
                                $.cresenity.append('custom_field_content','" . curl::base() . $this->_controller."add_custom_field','get',{'count':count,'rel':rel});                }

                        ";
                        $app->add_js($js);        
                        
                        //$this->generate_custom_field($repeater_data, $obj);
                        break;
                    }
                    default : {
                        $obj->add_field()->set_label(clang::__($label))->add_control($name,$type)->set_value($default_value);
                        break;
                    }
                }
            }
        }
        
        
        
        
    }
    