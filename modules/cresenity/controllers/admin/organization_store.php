<?php defined('SYSPATH') OR die('No direct access allowed.');
class Organization_Store_Controller extends CController {
	
	public function index($org_id) {
		$app = CApp::instance();
		$app->title(clang::__("Organization Store"));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Organization Store')->set_icon('plus')->set_link(curl::base()."admin/organization_store/add/".$org_id);
		
		$table = $app->add_table();
		
		
			
			
		$table->add_column('store_id')->set_label('ID');
		$table->add_column('code')->set_label('Code');
		$table->add_column('name')->set_label('Name');
		$table->add_column('type')->set_label('Type');
		$table->add_column('timezone')->set_label('Timezone');
		$table->add_column('api_key')->set_label('API Key');
		$cdb = CJDB::instance();
		
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;
	
	
		$data=$cdb->get('store',array("org_id"=>$org_id))->result_array();
		$table->set_data_from_array($data)->set_key('store_id');
		$table->set_title(clang::__("Store for ".$org_name ));
		
	
		$table->cell_callback_func(array("Organization_Store_Controller","cell_callback"),__FILE__);
		
	
		$actedit = $table->add_row_action();
		$actedit->set_label("Setting")->set_icon("wrench")->set_link(curl::base()."admin/organization_store/setting/".$org_id."/{store_id}");

		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/organization_store/edit/".$org_id."/{store_id}");
		$actedit = $table->add_row_action();
		$actedit->set_label("Delete")->set_icon("trash")->set_link(curl::base()."admin/organization_store/delete/".$org_id."/{store_id}")->set_confirm(true);

		echo $app->render();
		
	}
	
	public static function cell_callback($table,$col,$row,$text) {
		if($col=="type") {
			$types = cjson::decode($row['type']);
			$ret = array();
			foreach($types as $type) {
				$r = CJDB::instance()->get('store_type',array('store_type_id'=>$type));
				if($r->count()>0) {
					$name = $r[0]->name;
					$ret[]=$name;
				}
			}
			$ret = implode(",",$ret);
			return $ret;
		}
		return $text;
	}
	
	public function add($org_id) {
		$app = CApp::instance();
		$app->title(clang::__('Create Store'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$app->add_breadcrumb('Store',curl::base().'admin/organization_store/index/'.$org_id);
		$post = $this->input->post();

		$timezone = "Asia/Jakarta"; //default to Asia Jakarta, todo: set from master config
		
		//get organization information
		$cdb = CJDB::instance();
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;
		
		$store_id = "";
		$type = array();
		$code = "";
		$name = "";
		
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				$store_id= $post["store_id"];
				$type= $post["type"];
				$code= $post["code"];
				$name= $post["name"];
				$timezone= $post["timezone"];
				$api_key=cstore::generate_api_key($org_id);
				$data = array(
					"org_id"=>$org_id,
					"store_id"=>$store_id,
					"type"=>json_encode($type),
					"code"=>$code,
					"name"=>$name,
					"timezone"=>$timezone,
					"api_key"=>$api_key,
				);
				$r = $cdb->insert("store",$data);
				//create domain data on data folder
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Store")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/organization_store/index/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Store for ")." ".$org_name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$store_type_list = $cdb->get_list('store_type','store_type_id','name');
		$form->add_field()->set_label(clang::__('ID'))->add_control('store_id','text')->set_value($store_id);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Type'))->add_control('type','select')->set_list($store_type_list)->set_value($type)->set_multiple(true);
		$tzlist = ctimezone::timezone_list();
		$form->add_field('timezone-field')->set_label("Timezone")->add_control('timezone','select')->add_validation('required')->set_list($tzlist)->set_value($timezone);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		echo $app->render();
	}

	public function edit($org_id,$store_id) {
		$app = CApp::instance();
		$app->title(clang::__('Edit Store'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$app->add_breadcrumb('Store',curl::base().'admin/organization_store/index/'.$org_id);
		$post = $this->input->post();
		
		//get organization information
		$cdb = CJDB::instance();
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$org_name = $row->name;
		$org_code = $row->code;

		//get store
		$r = $cdb->get('store',array("org_id"=>$org_id,"store_id"=>$store_id));
		$row=$r[0];
		$type = json_decode($row->type);
		$name = $row->name;
		$code = $row->code;
		$timezone = $row->timezone;
		
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				$type= $post["type"];
				$code= $post["code"];
				$name= $post["name"];
				$timezone= $post["timezone"];
				$data = array(
					"type"=>json_encode($type),
					"code"=>$code,
					"name"=>$name,
					"timezone"=>$timezone,
				);				
				$r = $cdb->update("store",$data,array("org_id"=>$org_id,"store_id"=>$store_id));
				//create domain data on data folder
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Store")." \"".$name."\" ".clang::__("Successfully Edited")." !");
				
				curl::redirect('admin/organization_store/index/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Store ").$name." from ".$org_name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$store_type_list = $cdb->get_list('store_type','store_type_id','name');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Type'))->add_control('type','select')->set_list($store_type_list)->set_value($type)->set_multiple(true);
		$tzlist = ctimezone::timezone_list();
		$form->add_field('timezone-field')->set_label("Timezone")->add_control('timezone','select')->add_validation('required')->set_list($tzlist)->set_value($timezone);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		echo $app->render();
	}

	
	
	public function delete($org_id,$store_id) {
		if (strlen($org_id)==0) {
			curl::redirect('admin/home');
		}
		if (strlen($store_id)==0) {
			curl::redirect('organization_store/index/'.$org_id);
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
		
		
		$r = $cdb->get('store',array("store_id" => $store_id,"org_id" => $org_id));
		$store = null;
		if($r->count()>0) {
			$store = $r[0];
		}
		
		if ($error==0) {
			try {    
				$cdb->delete("store", array("store_id" => $store_id,"org_id" => $org_id));
			} catch (Exception $e) {
				$error++;
				$error_message = "Fail on delete, please call the administrator...";
			}
		}
        
		if ($error==0) {
			cmsg::add('success',"Store \"".$store->name."\" for ".$org_name." Successfully Deleted !");
		} else {
			//proses gagal
			cmsg::add('error',$error_message);
		}
		curl::redirect('admin/organization_store/index/'.$org_id);
	}
	
	
	public function setting($org_id,$store_id,$store_type_id="") {
		$org = corg::get($org_id);
		$store = cstore::get($org_id,$store_id);
		$app = CApp::instance();
		$app->title(clang::__('Store Setting'));
		$app->add_breadcrumb('Store',curl::base().'organization_store/'.$org_id);
		$app->add_breadcrumb('Organization',curl::base().'organization/');
		
		$this_store_types = cjson::decode($store->type);
		if(count($this_store_types)>0) {
			if(strlen($store_type_id)==0) {
				$store_type_id = $this_store_types[0];
			}
		}
		
		$config_path = MODPATH."cresenity"."/"."config"."/";
		
		
		
		$org_config_path = $config_path."org"."/";
		$store_config_path = $config_path."store".DIRECTORY_SEPARATOR;
		if(!is_dir($store_config_path)) mkdir($store_config_path);
		$store_config_path .= $org->code.DIRECTORY_SEPARATOR;
		if(!is_dir($store_config_path)) mkdir($store_config_path);
		$store_config_path .= $store->code.DIRECTORY_SEPARATOR;
		if(!is_dir($store_config_path)) mkdir($store_config_path);
		$store_config_file = $store_config_path.$store_type->code.EXT;
		
		
		//load master config
		$master_config_file = $config_path ."cconfig/cconfig".EXT;
		require $master_config_file;
		$mcfg = $config;
		
		$post = $this->input->post();
		//print_r($post);
		if($post!=null) {
			$text = "<?php\r\n";
			foreach($mcfg as $t) {
				$text.="\r\n//".$t["label"]." Config\r\n";
				foreach($t['config'] as $cfg) {
					$val = '';
					if(isset($post[$cfg['name']])) {
						$val = $post[$cfg['name']];
					}
					if(isset($cfg['multiple'])) {
						if(is_array($val)) {
							foreach($val as $k=>$v) {
								$val[$k]="'".$v."'";
							}
							$val = implode(",",$val);
							$val = "array(".$val.")";
						} else {
							$val = "'".$val."'";
						}
					} else {
						if($cfg['type']=='checkbox') {
							if(strlen($val)>0) {
								$val = "true";
							} else {
								$val = "false";
							}
						} else {
							$val = "'".$val."'";
						}
					}
					$text.="\$config['".$cfg['name']."']=".$val.";\r\n";
				}
			}
			file_put_contents($org_config_file,$text);
			cmsg::add('success','Success update org setting');
		}
		
		$config_file = $org_config_file;
		if(!file_exists($config_file)) $config_file = $cresenity_config_file;
		
		require $config_file;
		$ccfg = $config;
		unset($config);
		
		$widget = $app->add_widget()->set_title('Org Setting')->set_icon('wrench')->add_class('org-setting')->set_nopadding(false);
		$form = $widget->add_form();
		$tabs = $form->add_tabs();
		
		
		
		unset($config);
		$jsstmt="";
		$change_refresh = array();
		foreach($mcfg as $t) {
			$tab = $tabs->add_tab($t['name'])->set_title(clang::__($t['label']));
			
			foreach($t['config'] as $cfg) {
				$field = $tab->add_field('field-'.$cfg['name'])->set_label(clang::__($cfg['label']));
				$control = $field->add_control($cfg['name'],$cfg['type']);
				$val = $cfg["default"];
				if(isset($ccfg[$cfg["name"]])) {
					$val = $ccfg[$cfg["name"]];
				}
				if(isset($cfg["help"])&&strlen($cfg["help"])>0) {
					$field->set_info_text($cfg["help"]);
				}
				$control->set_value($val);
				if($val===true&&$cfg["type"]=="checkbox") {
					$control->set_checked($val);
				} 
				if($cfg["type"]=="checkbox") {
					$control->set_value("1");
					//$control->set_applyjs("switch");
					$control->set_applyjs("");
				} 
				if($cfg["type"]=="select") {
					$control->set_applyjs("");
					if(isset($cfg['list'])) {
						$control->set_list($cfg['list']);
					}
					if(isset($cfg['multiple'])) {
						$control->set_multiple($cfg['multiple']);
						$control->set_applyjs("select2");
					}
				}
				if(isset($cfg['requirement'])) {
					$cond = '';
					foreach($cfg['requirement'] as $k=>$v) {
						if (!in_array($k, $change_refresh)) {
							$change_refresh[]=$k;
						}
						if(strlen($cond)>0) $cond.="&&";
						if (is_bool($v) === true) {
							if($v===true) {
								$cond.="(jQuery('#".$k."').is(':checked'))";
							} else {
								$cond.="(!jQuery('#".$k."').is(':checked'))";
							}
						} else {
							$cond.="(jQuery('#".$k."').val()=='".$v."')";
						}
					}
					$jsstmt.="
						pare = jQuery('#".$cfg["name"]."').parent().parent();
						if(pare.hasClass('switch')) {
							pare = pare.parent().parent();
						}
						if(".$cond.") {
							pare.fadeIn('slow',function() {jQuery(this).show()});
						} else {
							pare.fadeOut('slow',function() {jQuery(this).hide()});
						}";
				}
				
			}			
			
		}
		
		$jschange = "";
		foreach($change_refresh as $v) {
			$jschange.="
				jQuery('#".$v."').change(function() { refresh_controls() });
			";
		}
		
		$js = "
			function refresh_controls() {
				".$jsstmt."
			}
			jQuery(document).ready(function() {
				".$jschange."
				refresh_controls();
			});
			
		";
		$form->add_action_list()->add_action()->set_submit(true)->set_label(clang::__('Save'));
		$app->add_js($js);
		
		echo $app->render();
	}
}