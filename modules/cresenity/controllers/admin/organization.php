<?php defined('SYSPATH') OR die('No direct access allowed.');
class Organization_Controller extends CController {
	
	public function index() {
		$org = CApp::instance();
		$org->title(clang::__("Organization"));
		$db=CDatabase::instance();
		
		$cdb = CJDB::instance();
		$data=$cdb->load('org')->data();
		
		
		$actions = $org->add_div()->add_class("row-fluid")->add_action_list();
		$actions->add_action()->set_label('Create Organization')->set_icon('plus')->set_link(curl::base()."admin/organization/add");
		
		$table = $org->add_table('org_table');
		
		$table->add_column('org_id')->set_label(clang::__("ID"));
		$table->add_column('code')->set_label(clang::__("Code"));
		$table->add_column('abbr')->set_label(clang::__("Abbr"));
		$table->add_column('name')->set_label(clang::__("Name"));
		$table->add_column('timezone')->set_label(clang::__("Timezone"));
		$table->set_data_from_array($data)->set_key('org_id');
		$table->set_title(clang::__("Org"));
		
		$actedit = $table->add_row_action();
		$actedit->set_label("Setting")->set_icon("wrench")->set_link(curl::base()."admin/organization/setting/{param1}");

		$actedit = $table->add_row_action();
		$actedit->set_label("Edit")->set_icon("pencil")->set_link(curl::base()."admin/organization/edit/{org_id}");
	
		$actedit = $table->add_row_action();
		$actedit->set_label("Domain")->set_icon("globe")->set_link(curl::base()."admin/organization_domain/index/{org_id}");

		$actedit = $table->add_row_action();
		$actedit->set_label("Store")->set_icon("shopping-cart")->set_link(curl::base()."admin/organization_store/index/{org_id}");

		$actedit = $table->add_row_action();
		$actedit->set_label("Delete")->set_icon("trash")->set_link(curl::base()."admin/organization/delete/{org_id}")->set_confirm(true);
		
		echo $org->render();
		
	}
	public function add() {
		$app = CApp::instance();
		$app->title(clang::__('Create Organization'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$cdb = CJDB::instance();
		$post = $this->input->post();
		$code = "";
		$abbr = "";
		$name = "";
		$email = "";
		$password = "";
		$timezone = "Asia/Jakarta"; //default to Asia Jakarta, todo: set from master config
		$org_id = "";
		if($post!=null) {
			$error = 0;
			$error_message = "";
			try {
				//check org_id exists
				$org_id = $post["org_id"];
				$name= $post["name"];
				$code= $post["code"];
				$abbr= $post["abbr"];
				$email= $post["email"];
				$password= $post["password"];
				$password = md5($password);
				$data = array(
					"org_id"=>$org_id,
					"code"=>$code,
					"abbr"=>$abbr,
					"name"=>$name,
					"timezone"=>$timezone,
					"email"=>$email,
					
				);
				$r = $cdb->insert("org",$data);
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Application")." \"".$name."\" ".clang::__("Successfully Created")." !");
				
				curl::redirect('admin/organization/edit/'.$org_id);
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		
		
		
		$widget = $app->add_widget()->set_icon('plus')->set_title(clang::__("Create Organization")." ".$name);;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Organization ID'))->add_control('org_id','text')->set_value($org_id);
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Abbr'))->add_control('abbr','text')->set_value($abbr);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Email'))->add_control('email','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Password'))->add_control('password','text')->set_value($password);
		$tzlist = ctimezone::timezone_list();
		$form->add_field('timezone-field')->set_label("Timezone")->add_control('timezone','select')->add_validation('required')->set_list($tzlist)->set_value($timezone);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		
		echo $app->render();
	}
	public function edit($org_id) {
		$cdb = CJDB::instance();
		$app=CApp::instance();
		$app->title(clang::__('Add Organization'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$r = $cdb->get('org',array("org_id"=>$org_id));
		$row=$r[0];
		$post = $this->input->post();
		$name = $row->name;
		$code = $row->code;
		$abbr = $row->abbr;
		$email = $row->email;
		$password = '';
		$timezone = $row->timezone;
	
		if ($post!=null) {
      		$error = 0;
			$error_message = "";
			try {
				$name= $post["name"];
				$code= $post["code"];
				$abbr= $post["abbr"];
				$timezone= $post["timezone"];
				$email= $post["email"];
				$password= $post["password"];
				$data = array(
					"name"=>$name,
					"code"=>$code,
					"abbr"=>$abbr,
					"timezone"=>$timezone,
					"email"=>$email,
				);
				if(strlen($password)>0) {
					$password = md5($password);
					$data = array_merge($data,array(
						"password"=>$password,
					));
				}
				$cdb->update("org",$data,array("org_id"=>$org_id));
				$password = '';
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();

			}
			if ($error==0) {
				
				cmsg::add('success',clang::__("Organization")." \"".$name."\" ".clang::__("Successfully Modified")." !");
				
				curl::redirect('admin/organization');
			} else {
				cmsg::add("error",$error_message);
			}	
		}
		$widget = $app->add_widget()->set_icon('pencil')->set_title(clang::__("Edit Organization")." [".$name."]");;
		$form = $widget->add_form();
		$form->set_method('post');
		$form->add_field()->set_label(clang::__('Code'))->add_control('code','text')->set_value($code);
		$form->add_field()->set_label(clang::__('Abbr'))->add_control('abbr','text')->set_value($abbr);
		$form->add_field()->set_label(clang::__('Name'))->add_control('name','text')->set_value($name);
		$form->add_field()->set_label(clang::__('Email'))->add_control('email','text')->set_value($email);
		$form->add_field()->set_label(clang::__('Password'))->add_control('password','password')->set_value('');
		$tzlist = ctimezone::timezone_list();
		$form->add_field('timezone-field')->set_label("Timezone")->add_control('timezone','select')->add_validation('required')->set_list($tzlist)->set_value($timezone);
		$form->add_action_list()->set_style('form-action')->add_action()->set_submit(true)->set_label(clang::__("Submit"));
		
		
		echo $app->render();
	}
	
	
	
	public function setting($org_id) {
        $org = corg::get($org_id);
		$cdb = CJDB::instance();
		$app = CApp::instance();
		$app->title(clang::__('Organization Setting'));
		$app->add_breadcrumb('Organization',curl::base().'admin/organization/');
		$domain = "";
		$domain_data=$cdb->get('domain',array("org_id"=>$org_id))->result_array();
	
		$domain_list = "";
		if($domain_data==null) {
			$app->add_widget()->set_icon('comment')->set_title('Error')->add('<div class=\"well\">Please set minimal one domain for this org<br/><a href="'.curl::base().'admin/organization_domain/index/'.$org_id.'" title="Set Domain">Set Domain</a></div>');
			echo $app->render();
			exit;
		}
		
		
		if(isset($_GET["domain"])) {
			$domain = $_GET["domain"];
		}
		if(strlen($domain)==0) {
			foreach($domain_data as $v) {
				$domain=$v["domain"];
				break;
			}
		}
		$app_code = "";
		foreach($domain_data as $d) {
			$r = $cdb->get('app',array('app_id'=>$d['app_id']));
			$name = $r[0]->name;
			$code = $r[0]->code;
			$domain_list[$d['domain']]=$d['domain']." [".$name."]";
			if($domain==$d['domain']) {
				$app_code = $code;
			}
		}
		
		
		//get app_id
		
		$config_path = DOCROOT."config".DIRECTORY_SEPARATOR;
		
		$org_config_path = $config_path."app".DIRECTORY_SEPARATOR;
		if(!is_dir($org_config_path)) mkdir($org_config_path);
		$org_config_path = $org_config_path.$org->code."/";
		if(!is_dir($org_config_path)) mkdir($org_config_path);
		$org_config_path = $org_config_path.$domain.DIRECTORY_SEPARATOR;
		if(!is_dir($org_config_path)) mkdir($org_config_path);
		$org_config_file = $org_config_path."app".EXT;
		$cresenity_config_file = $config_path."app".EXT;
		
		//load master config
		$master_config_file = $config_path ."master".DIRECTORY_SEPARATOR.$app_code.DIRECTORY_SEPARATOR."config".EXT;
		require $master_config_file;
		$mcfg = $config;
		
		$post = $_POST;
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
								$val[$k]="'".addslashes($v)."'";
							}
							$val = implode(",",$val);
							$val = "array(".$val.")";
						} else {
							$val = "'".addslashes($val)."'";
						}
					} else {
						if($cfg['type']=='checkbox') {
							if(strlen($val)>0) {
								$val = "true";
							} else {
								$val = "false";
							}
						} else {
							$val = "'".addslashes($val)."'";
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
		
		
		$widget = $app->add_widget()->set_title('Application')->set_icon('desktop')->add_class('org-application')->set_nopadding(false);
		$form = $widget->add_form()->set_method('get');
		$form->add_field()->set_label(clang::__('Application'))->add_control('domain','select')->set_list($domain_list)->set_value($domain)->set_submit_onchange(true);
		
		$widget = $app->add_widget()->set_title('Org Setting')->set_icon('wrench')->add_class('org-setting')->set_nopadding(false);
		$form = $widget->add_form();
		$tabs = $form->add_tab_static_list();
		
		
		
		unset($config);
		$jsstmt="";
		$change_refresh = array();
		foreach($mcfg as $t) {
			$tab = $tabs->add_tab($t['name'])->set_title(clang::__($t['label']));
			
			foreach($t['config'] as $cfg) {
				$field = $tab->add_field('field-'.$cfg['name']);
				if($cfg["type"]!="checkbox") {
					$field->set_label(clang::__($cfg['label']));
				}
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
					$control->set_label(clang::__($cfg['label']));
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
                    if(strlen($cond) == 0) $cond = 'false';
					$jsstmt.="
						
						pare = jQuery('#".$cfg["name"]."').parent().parent();
						
						if(pare.hasClass('controls')) {
							pare = pare.parent();
						}
						if(".$cond.") {
							pare.fadeIn('slow',function() {jQuery(this).show()});
						} else {
							pare.fadeOut('slow',function() {jQuery(this).hide()});
						}";
				}
				//$tab->add_div()->add_class('clear-both');
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