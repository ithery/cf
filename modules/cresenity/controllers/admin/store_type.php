<?php defined('SYSPATH') OR die('No direct access allowed.');
class Store_type_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Store Type"));
		$db=CDatabase::instance();
		
		$cdb = CJDB::instance();
		$data=$cdb->get('store_type')->result_array();
		
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Store Type')->set_icon('plus')->set_link(curl::base()."admin/store_type/add");
		
		$table = $app->add_table('store_type_table');
		
		$table->add_column('store_type_id')->set_label(clang::__("ID"));
		$table->add_column('code')->set_label(clang::__("Code"));
		$table->add_column('name')->set_label(clang::__("Name"));
		$table->set_data_from_array($data)->set_key('store_id');
		$table->set_title(clang::__("Store Type"));
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/store_type/edit/{store_type_id}");
	
		$actedit = $table->add_row_action();
		$actedit->set_label("Delete")->set_icon("trash")->set_link(curl::base()."admin/store_type/delete/{store_type_id}")->set_confirm(true);
		
		echo $app->render();
		
	}
	public function add() {
		$app = CApp::instance();
		$app->title(clang::__('Create Store Type'));
		$app->add_breadcrumb('Store Type',curl::base().'admin/store_type/');
		$cdb = CJDB::instance();
		$post = $this->input->post();
		$code = "";
		$name = "";
		$store_type_id = "";
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				//check app_id exists
				$store_type_id = $post["store_type_id"];
				$name= $post["name"];
				$code= $post["code"];
				$data = array(
					"store_type_id"=>$store_type_id,
					"code"=>$code,
					"name"=>$name,
					
				);
				$r = $cdb->insert("store_type",$data);
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Store Type")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/store_type/edit/'.$store_type_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Store Type")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Store Type ID'))->add_control('store_type_id','text')->set_value($store_type_id);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	public function edit($store_type_id) {
		$cdb = CJDB::instance();
		$app=CApp::instance();
		$app->title(clang::__('Add Store Type'));
		$app->add_breadcrumb('Store Type',curl::base().'admin/store_type/');
		$r = $cdb->get('store_type',array("store_type_id"=>$store_type_id));
		$row=$r[0];
		$post = $this->input->post();
		$name = $row->name;
		$code = $row->code;
	
		if ($post!=null) {
      		$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$data = array(
					"name"=>$name,
					"code"=>$code,
					
				);
				$cdb->update("app",$data,array("app_id"=>$app_id));
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Store Type")." \"".$name."\" ".clang::__("Successfully Modified")." !");
				
				curl::redirect('admin/store_type');
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Store Type")." [".$name."]");;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		echo $app->render();
	}
	
	

}