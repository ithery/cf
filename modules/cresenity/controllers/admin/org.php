<?php defined('SYSPATH') OR die('No direct access allowed.');
class Org_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Org"));
		$db=CDatabase::instance();
		
		$actions = $app->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Org')->set_icon('plus');
		
		$table = $app->add_table('org_table');
		
			
			
		
		$table->add_column('code')->set_label(clang::__("Code"));
		$table->add_column('name')->set_label(clang::__("Name"));
		$table->add_column('app_domain')->set_label(clang::__("App Domain"));
		$table->add_column('front_domain')->set_label(clang::__("Front Domain"));
		$table->add_column('back_domain')->set_label(clang::__("Back Domain"));
		$table->add_column('timezone')->set_label(clang::__("Timezone"));
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$q = ' select * from org where status>0';
		$table->set_data_from_query($q)->set_key('org_id');
		$table->set_title(clang::__("Org"));
		
	
		
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/org/edit/{param1}");


		$actedit = $table->add_row_action();
		$actedit->set_label("Org Setting")->set_icon("wrench")->set_link(curl::base()."admin/org/setting/{param1}");
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Store")->set_icon("shopping-cart")->set_link(curl::base()."admin/store/index/{org_id}");
		

	
	
		
		echo $app->render();
		
	}
	public function add() {
		$app = CApp::instance();
		$app->title(clang::__('Create Org'));
		$app->add_breadcrumb('Org',curl::base().'admin/org/');
		$db = CDatabase::instance();
		$post = $this->input->post();
		$code = "";
		$name = "";
		
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$data = array(
					"name"=>$name,
					"code"=>$code,
					
				);
				$r = $db->insert("org",$data);
				$org_id = $r->insert_id();
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Title")." ".clang::__("Sales")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/org/edit/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Org")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	public function edit($org_id) {
		$org = corg::get($org_id);
		$app = CApp::instance();
		$db = CDatabase::instance();
		$app->title(clang::__('Edit Org'));
		$app->add_breadcrumb('Org',curl::base().'admin/org/');
		
		$post = $this->input->post();
		$name = $org->name;
		$code = $org->code;
		$app_domain = $org->app_domain;
		$front_domain = $org->front_domain;
		$back_domain = $org->back_domain;
		if ($post!=null) {
      		$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$app_domain= $post["app_domain"];
				$front_domain= $post["front_domain"];
				$back_domain= $post["back_domain"];
				$data = array(
					"name"=>$name,
					"code"=>$code,
					"app_domain"=>$app_domain,
					"front_domain"=>$front_domain,
					"back_domain"=>$back_domain,
					
				);
				$db->update("org",$data,array("org_id"=>$org_id));
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Org")." \"".$name."\" ".clang::__("Successfully Modified")." !");
				
				curl::redirect('admin/org');
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Org")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('App Domain'))->add_control('app_domain','text')->set_value($app_domain);
		$form->add_field()->set_label(clang::__('Front Domain'))->add_control('front_domain','text')->set_value($front_domain);
		$form->add_field()->set_label(clang::__('Back Domain'))->add_control('back_domain','text')->set_value($back_domain);
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