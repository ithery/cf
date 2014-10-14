<?php
defined ('SYSPATH') OR die('No direct access allowed.');
class User_rights_Controller extends CController {
   
    public static function cell_callback($table,$col,$row,$text) {
		$db = CDatabase::instance();
		$is_leaf = cmenu::is_leaf($row["menu_id"]);
		$level  = $row["level"];
		//print_r($row['a']);
		switch($col) {
			case "caption":
				$indent = str_repeat("&nbsp;",($level)*4);
				$hidden_role_id = CFormInputHidden::factory($col.'_'.$row['menu_id'])
					->set_name("menu_role_id".'_'.$row['menu_id'])
					->set_type('hidden')
					->set_value($row["menu_role_id"]);
				
				return $indent.$text.$hidden_role_id->html();
				//return '<span class="input"><input name="provider_voucher_id[]" type="hidden" value="'.html::specialchars($row['provider_voucher_id']).'" /><input name="format[]" type="text" value="'.html::specialchars($text).'" class="input-unstyled"/> </span>';
			break;
			case "access":
			case "add":
			case "edit":
			case "delete":
			case "confirm":
			case "download":
				if($row["controller"]=="") return "";
				if($col=="access"||$row["have_".$col]>0){
				$cb = CFormInputCheckbox::factory($col.'_'.$row['menu_id'])
					->set_name($col.'_'.$row['menu_id'])
					->set_type('checkbox')
					->set_value("1")
					->set_checked($row['can_'.$col]>0);
				return $cb->html();
				}
			break;
			
		}
		return $text;
	}
	public function index() {
		$app = CApp::instance();
		
		$error=0;
		
		$app->title(clang::__('User Rights'));
		
		$post = $_POST;
		$db = CDatabase::instance();
		$user = $app->user();
		$org = $app->org();
		$org_id = "";
		
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$app_id="";
		$role_id="";
		$user = $app->user();
		if ($post!=null) {
			
			try{
				$db->begin();
				$app_id = 1;
				if(isset($post["app_id"])) {
					$app_id = $post["app_id"];
				}
				$role_id = $post["role_id"];
				$q = "select menu_id from menu where status>0";
				$r = $db->query($q);
				foreach($r as $row) {
					$menu_id = $row->menu_id;
					if(isset($post["menu_role_id_".$row->menu_id])) {
						$can_add = "0";
						if (isset($post["add_".$row->menu_id])) $can_add =$post["add_".$row->menu_id];
						$can_edit = "0";
						if (isset($post["edit_".$row->menu_id])) $can_edit =$post["edit_".$row->menu_id];
						$can_delete = "0";
						if (isset($post["delete_".$row->menu_id])) $can_delete =$post["delete_".$row->menu_id];
						$can_confirm = "0";
						if (isset($post["confirm_".$row->menu_id])) $can_confirm =$post["confirm_".$row->menu_id];
						$can_download = "0";
						if (isset($post["download_".$row->menu_id])) $can_download =$post["download_".$row->menu_id];
						$can_access = "0";
						if (isset($post["access_".$row->menu_id])) $can_access =$post["access_".$row->menu_id];
						$menu_role_id = $post["menu_role_id_".$row->menu_id];
						if (strlen($menu_role_id)==0) $menu_role_id="0";
						$data = array(
							"can_add"=>$can_add,
							"can_edit"=>$can_edit,
							"can_delete"=>$can_delete,
							"can_confirm"=>$can_confirm,
							"can_download"=>$can_download,
							"status"=>$can_access,
						);
						if ($menu_role_id!="0") {
							//update this id
							$data = array_merge($data,array(
								"updated"=>date("Y-m-d H:i:s"),
								"updatedby" => $user->username
							));

							$db->update("menu_role",$data,array("menu_role_id"=>$menu_role_id));
						} else {
							$data = array_merge($data,array(
								"menu_id"=>$menu_id,
								"role_id"=>$role_id,
								"created"=>date("Y-m-d H:i:s"),
								"createdby" => $user->username
							));
							$db->insert("menu_role",$data);

						}

					}
					
					
				}
				
				
			
			}catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();;
			}
			if ($error==0) {
				$db->commit();
				cmsg::add("success","User Rights Successfully Modified !");
			} else {
				$db->rollback();
				cmsg::add("error",$error_message);
			}
		}

		$html= '';
		$app_list = cdbutils::get_list("select a.app_id,a.name as name from app a order by a.name asc;");
		$app_id = "";
		if(isset($_GET["app_id"])) {
			$app_id = $_GET["app_id"];
		}
		if(strlen($app_id)==0) {
			if(isset($_POST["app_id"])) {
				$app_id = $_POST["app_id"];
			}
		}
		if(strlen($app_id)==0) {
			foreach($app_list as $k=>$v) {
				$app_id = $k;
				break;
			}
		}
	    
		$role_list = cdbutils::get_list("select a.role_id,a.name as name from roles a where status>0 and org_id=".$db->escape($org_id)." order by a.role_id asc;");
		$role_id = "";
		if(isset($_GET["role_id"])) {
			$role_id = $_GET["role_id"];
		}
		if(strlen($role_id)==0) {
			if(isset($_POST["role_id"])) {
				$role_id = $_POST["role_id"];
			}
		}
		if(strlen($role_id)==0) {
			foreach($role_list as $k=>$v) {
				$role_id = $k;
				break;
			}
		}
		
		
		$form = CForm::factory('user_right_form')->set_method('GET');
		$form->add_field('application-field')->set_label('Application')->add_control('app_id','select')->set_value($app_id)->set_list($app_list)->add_validation(null);
		$form->add_field('role-field')->set_label('Role')->add_control('role_id','select')->set_value($role_id)->set_list($role_list)->add_validation(null);
		
		$r=$app->add_row_fluid();
		$w=$r->add_widget();
		$w->add($form);
		$w->set_title('User Rights');
		$data = cmenu::populate_menu_user_rights_as_array($app_id,$role_id);
		
		
		
		$table = CTable::factory('user_rights_table');
		$table->set_key("menu_id");
		$table->set_data_from_array($data);
		$table->add_column('caption')->set_label('Caption');
		$table->add_column('access')->set_label('Access')->set_align('center');
		$table->add_column('add')->set_label('Add')->set_align('center');
		$table->add_column('edit')->set_label('Edit')->set_align('center');
		$table->add_column('delete')->set_label('Delete')->set_align('center');
		$table->cell_callback_func(array("User_rights_Controller","cell_callback"));
		$table->set_apply_data_table(false);
		/*
			->set_header_sortable(false)
			->set_apply_data_table(true)
			->enable_numbering()
			->set_option("pagination",false)
			->set_option("height",200)
			->cell_callback_func(array("User_rights_Controller","cell_callback"))
			;
	
		*/
		$additional_js = "";
		
		$form = CForm::factory('user_rights_form')->set_method('POST');
		$form->add_control('app_id','hidden')->set_value($app_id);
		$form->add_control('role_id','hidden')->set_value($role_id);
		
		
		$r=$form->add_row_fluid();
		$w=$r->add_widget();
		$w->add($table);
		$w->set_nopadding(true);
		$w->set_title('Permission');
		
		$actions = CActionList::factory('act_user_rights');
		$act_next = CAction::factory('submit')->set_label('Submit')->set_submit(true);
		
		
		$actions->add($act_next);
		$actions->set_style('form-action');
		
		
		$app->add_js("
			jQuery(document).ready(function() {
				$('#app_id, #role_id').change(function() {
					$('#user_right_form').submit();
				});
			});
		");
	    $form->add($actions);
		$app->add($form);
        echo $app->render();
	}
	
	
}
?>