<?php defined('SYSPATH') OR die('No direct access allowed.');
class Application_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Application"));
		$db=CDatabase::instance();
		
		$cdb = CJDB::instance();
		$data=$cdb->get('app')->result_array();
		
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Application')->set_icon('plus')->set_link(curl::base()."admin/application/add");
		
		$table = $app->add_table('app_table');
		
		$table->add_column('app_id')->set_label(clang::__("ID"));
		$table->add_column('code')->set_label(clang::__("Code"));
		$table->add_column('name')->set_label(clang::__("Name"));
		$table->set_data_from_array($data)->set_key('app_id');
		$table->set_title(clang::__("Application"));
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/application/edit/{app_id}");
	
		$actedit = $table->add_row_action();
		$actedit->set_label("Delete")->set_icon("trash")->set_link(curl::base()."admin/application/delete/{app_id}")->set_confirm(true);
		
		echo $app->render();
		
	}
	public function add() {
		$app = CApp::instance();
		$app->title(clang::__('Create Application'));
		$app->add_breadcrumb('Application',curl::base().'admin/application/');
		$cdb = CJDB::instance();
		$post = $this->input->post();
		$code = "";
		$name = "";
		$app_id = "";
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				//check app_id exists
				$app_id = $post["app_id"];
				$name= $post["name"];
				$code= $post["code"];
				$data = array(
					"app_id"=>$app_id,
					"code"=>$code,
					"name"=>$name,
					
				);
				$r = $cdb->insert("app",$data);
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Application")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/application/edit/'.$app_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Application")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Application ID'))->add_control('app_id','text')->set_value($app_id);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	public function edit($app_id) {
		$cdb = CJDB::instance();
		$app=CApp::instance();
		$app->title(clang::__('Add Application'));
		$app->add_breadcrumb('Application',curl::base().'admin/application/');
		$r = $cdb->get('app',array("app_id"=>$app_id));
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
				
				cmsg::add('success',clang::__("Application")." \"".$name."\" ".clang::__("Successfully Modified")." !");
				
				curl::redirect('admin/application');
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Application")." [".$name."]");;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		echo $app->render();
	}
	
	public function setting($org_id) {
		$org = corg::get($org_id);
		
		$app = CApp::instance();
		$app->title(clang::__('Org Setting'));
		$app->add_breadcrumb('Org',curl::base().'admin/org/');
		
		$config_path = MODPATH."cresenity"."/"."config"."/";
		
		$org_config_path = $config_path."org"."/";
		if(!is_dir($org_config_path)) mkdir($org_config_path);
		$org_config_file = $org_config_path.$org->code.EXT;
		$cresenity_config_file = $config_path."cresenity".EXT;
		
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