<?php defined('SYSPATH') OR die('No direct access allowed.');
class Organization_domain_Controller extends CController {
	
	public function index($org_id) {
		$app = CApp::instance();
		$app->title(clang::__("Organization Domain"));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Organization Domain')->set_icon('plus')->set_link(curl::base()."admin/organization_domain/add/".$org_id);
		
		$table = $app->add_table();
		
		
			
			
		$table->add_column('domain')->set_label('Domain');
		$table->add_column('app_id')->set_label('Application');
		$cdb = CJDB::instance();
		
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;
	
	
		$data=$cdb->get('domain',array("org_id"=>$org_id))->result_array();
		$table->set_data_from_array($data)->set_key('domain_id');
		$table->set_title(clang::__("Domain for ".$org_name ));
		
	
		$table->cell_callback_func(array("Organization_domain_Controller","cell_callback"),__FILE__);
		
	
		$actedit = $table->add_row_action();
		$actedit->set_label("Delete")->set_icon("trash")->set_link(curl::base()."admin/organization_domain/delete/".$org_id."/{app_id}")->set_confirm(true);
				
		echo $app->render();
		
	}
	
	public static function cell_callback($table,$col,$row,$text) {
		if($col=="app_id") {
			
			$r = CJDB::instance()->get('app',array('app_id'=>$row['app_id']));
			$name = $r[0]->name;
			return $name;
		}
		return $text;
	}
	public function add($org_id) {
		$app = CApp::instance();
		$app->title(clang::__('Create Domain'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$app->add_breadcrumb('Organization Domain',curl::base().'admin/organization_domain/index/'.$org_id);
		$post = $this->input->post();
		
		//get organization information
		$cdb = CJDB::instance();
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;
		
		$app_id = "";
		$domain = "";
		
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				$app_id= $post["app_id"];
				$domain= $post["domain"];
				$app_code = "";
				$app = $cdb->get("app",array("app_id"=>$app_id));
				$app_code = $app[0]->code;
				$data = array(
					"org_id"=>$org_id,
					"app_id"=>$app_id,
					"app_code"=>$app_code,
					"domain"=>$domain,
					
				);
				$r = $cdb->insert("domain",$data);
				cdata::set($domain,$data,'domain');
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Domain")." \"".$domain."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/organization_domain/index/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Domain for ")." ".$org_name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$app_list = $cdb->get_list('app','app_id','name');
		unset($app_list[0]);
		$form->add_field()->set_label(clang::__('Application'))->add_control('app_id','select')->set_list($app_list)->set_value($app_id);
		$form->add_field()->set_label(clang::__('Domain'))->add_control('domain','text')->set_value($domain);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	
	
	public function delete($org_id,$app_id) {
		if (strlen($org_id)==0) {
			curl::redirect('admin/home');
		}
		if (strlen($app_id)==0) {
			curl::redirect('organization_domain/'.$org_id);
		}
		$app = CApp::instance();
		$cdb = CJDB::instance();
		$q = '';
		$error =0; 
		
		
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;
		
		
		$r = $cdb->get('domain',array("app_id" => $app_id,"org_id" => $org_id));
		$domain = null;
		if($r->count()>0) {
			$domain = $r[0];
		}
		
		if ($error==0) {
			try {    
				$cdb->delete("domain", array("app_id" => $app_id,"org_id" => $org_id));
				cdata::delete($domain->domain,'domain');
			} catch (Exception $e) {
				$error++;
				$error_message = "Fail on delete, please call the administrator...";
			}
		}
        
		if ($error==0) {
			cmsg::add('success',"Domain \"".$domain->domain."\" for ".$org_name." Successfully Deleted !");
		} else {
			//proses gagal
			cmsg::add('error',$error_message);
		}
		curl::redirect('admin/organization_domain/index/'.$org_id);
	}
	
}