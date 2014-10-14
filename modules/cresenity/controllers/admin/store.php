<?php defined('SYSPATH') OR die('No direct access allowed.');
class Store_Controller extends CController {
	
	public function index($org_id) {
		$app = CApp::instance();
		$app->title(clang::__("Store"));
		$db=CDatabase::instance();
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Store')->set_icon('plus')->set_link(curl::base()."admin/store/add/".$org_id);
		
		$table = $app->add_table('store_table');
		
			
			
		$table->add_column('code')->set_label('Code');
		$table->add_column('name')->set_label('Name');
		$table->add_column('store_key')->set_label('Key');
		$table->add_column('description')->set_label('Description');
//		$table->add_column('warehouse_name')->set_label('Warehouse');
		
		$q = ' 
			select 
				* 
			from 
				store 
			where 
				status>0
				and org_id='.$org_id.'
		';
		$table->set_data_from_query($q)->set_key('store_id');
		$table->set_title(clang::__("Store"));
		
	
		
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/store/edit/".$org_id."/{store_id}");

		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("trash")->set_link(curl::base()."admin/store/delete/".$org_id."/{store_id}");
				
		echo $app->render();
		
	}
	public function add($org_id) {
		$app = CApp::instance();
		$app->title(clang::__('Create Store'));
		$app->add_breadcrumb('Store',curl::base().'admin/store/index/'.$org_id);
		$db = CDatabase::instance();
		$post = $this->input->post();
		$code = "";
		$name = "";
		$description = "";
		$key = "";
		
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$description= $post["description"];
				$key=cstore::generate_key($org_id);
				if (strlen($key)==0){
					$error++;
					$error_message="Failed on Generate Key";
				}
				$data = array(
					"org_id"=>$org_id,
					"name"=>$name,
					"code"=>$code,
					"store_key"=>$key,
					"description"=>$description,
					
				);
				$r = $db->insert("store",$data);
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Title")." ".clang::__("Store")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/store/index/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Store")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field('address-field')->set_label('Description')->add_control('description','textarea')->add_validation(null)->set_value($description);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	public function edit($org_id,$store_id) {
		$store = cstore::get($org_id,$store_id);
		$app = CApp::instance();
		$db = CDatabase::instance();
		$app->title(clang::__('Edit Store'));
		$app->add_breadcrumb('Store',curl::base().'admin/store/index'.$org_id);
		
		$post = $this->input->post();
		$name = $store->name;
		$code = $store->code;
		$description = $store->description;
		if ($post!=null) {
      		$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$description= $post["description"];
				$data = array(
					"name"=>$name,
					"code"=>$code,
					"description"=>$description,
				);
				$db->update("store",$data,array("org_id"=>$org_id,"store_id"=>$store_id));
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Store")." \"".$name."\" ".clang::__("Successfully Modified")." !");
				
				curl::redirect('admin/store/index/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Store")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field('address-field')->set_label('Description')->add_control('description','textarea')->add_validation(null)->set_value($description);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		echo $app->render();
	}	

}