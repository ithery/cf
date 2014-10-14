<?php defined('SYSPATH') OR die('No direct access allowed.');
class Roles_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$db = CDatabase::instance();
		$app->title("Roles");
		
		$cdb = CJDB::instance();
		$org_list=$cdb->get_list('org','org_id','name');
		foreach($org_list as $k=>$v) {
			$org_id = $k;
			break;
		}
		$get = $_GET;
		if(isset($get["org_id"])) {
			$org_id = $get["org_id"];
		}
		$widget = $app->add_widget()->set_icon('filter')->set_title('Filter');
		$form = $widget->add_form()->set_method('get');
		$form->add_field()->set_label(clang::__('Organization'))->add_control('org_id','select')->set_value($org_id)->set_list($org_list)->set_submit_onchange(true);
		
		
		$actions = $app->add_div()->add_class('row-fluid')->add_div()->add_class('span12')->add_action_list();
		$actions->add_action()->set_label(" ".clang::__("Add")." ".clang::__("Roles"))->set_icon("plus")->set_link(curl::base()."admin/roles/add/".$org_id);
		$actions->add_action()->set_label(" ".clang::__("")." ".clang::__("Change Order"))->set_icon("refresh")->set_link(curl::base()."admin/roles/ordering/".$org_id);
			
		
		
		$table = $app->add_table();
		$table->add_column('parent_name')->set_label('Parent');
		$table->add_column('name')->set_label('Name');
		$table->add_column('created')->set_label('Created')->set_editable(false);
		$table->add_column('createdby')->set_label('Created By')->set_editable(false);
		$q = "select p.name as parent_name,r.role_id,r.name,r.created,r.createdby,r.updated from roles as r left join roles as p on p.role_id=r.parent_id where r.status>0";
		if(strlen($org_id)>0) {
			$q.= " and r.org_id=".$db->escape($org_id)."";
		}
		
		$table->set_data_from_query($q)->set_key('role_id');

		$table->set_title('Roles');
		
		
		$actedit = $table->add_row_action('edit');
		$actedit->set_label("")->set_icon("pencil")->set_link(curl::base()."admin/roles/edit/".$org_id."/{param1}");
		$actedit = $table->add_row_action('delete');
		$actedit->set_label("")->set_icon("trash")->set_link(curl::base()."admin/roles/delete/".$org_id."/{param1}")->set_confirm(true);
				
					

		
	
		
		echo $app->render();
		
	}
	public function add($org_id=null) {
		$this->edit($org_id);
	}
	public function edit($org_id=null,$id="") {
		$app = CApp::instance();
		$title = clang::__("Edit")." ".clang::__("Role");
		$icon = "pencil";
		if($id=="") {
			$title = clang::__("Add")." ".clang::__("Role");
			$icon = "plus";
		
		}
		$post = $_POST;
		$db = CDatabase::instance();
		$is_add = 0;
		if (strlen($id)==0) {
			$is_add = 1;
		}

		$post = $_POST;
		$name = "";
		$description = "";
		$parent_id = "";
		
		if ($post!=null) {
      
			$error = 0;
			$error_message = "";
			try {
                                $parent_id = null;
                                if(isset($post["parent_id"])) $parent_id = $post["parent_id"];
				$name= $post["name"];
				$description= $post["description"];
				//checking
				if ($error==0) {
					if (strlen($name)==0) {
						$error_message ="Role name is required !";
						$error++;
					}
				}

				
				if ($error==0) {
					$qcheck = "select * from roles where status>0 and name=".$db->escape($name)."";
					if(strlen($org_id)>0) {
						$qcheck .= " and org_id=".$db->escape($org_id)." ";
					}
					if ($is_add==0) $qcheck .= " and name<>".$db->escape($name)."";
					$rcheck = $db->query($qcheck);
					if ($rcheck->count()>0) {
						$error_message ="Role name is already exist, please try another name !";
						$error++;
					}
				}        
				
				$parent = crole::get($parent_id);
				$depth = 0;
				if($parent!=null) {
					$depth = $parent->depth+1;
				}
				
				if ($error==0) {
					$data=array (
						"org_id"				=> $org_id,
						"parent_id"				=> $parent_id,
						"depth" 	=>$depth,
						"name"				=> $name,
						"description"		=> $description,
						
					);
					if(strlen($id)==0) {
						$data=array_merge($data, array (
							"created"   => date("Y-m-d H:i:s"),
							"createdby" =>  'administrator',
							"updated"   => date("Y-m-d H:i:s"),
							"updatedby" =>  'administrator',
						));
						$db->insert("roles", $data);
						$tree = CTreeDB::factory('roles');
						$tree->rebuild_tree();
					} else {
						$data=array_merge($data, array (
							"updated"   => date("Y-m-d H:i:s"),
							"updatedby" =>  'administrator'
						));
						$db->update("roles", $data,array("role_id" => $id));
						$tree = CTreeDB::factory('roles');
						$tree->rebuild_tree();
					}
				}
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Error, call administrator...".$e->getMessage();;
			}
			if ($error==0) {
				if ($id>0) {
					
					cmsg::add("success","Role \"".$name."\" Successfully Modified !");
				} else {
					cmsg::add("success","Role \"".$name."\" Successfully Added !");
				}
				curl::redirect("admin/roles?org_id=".$org_id);
			} else {
				cmsg::add("error",$error_message);
			}
              
		} else if(strlen($id)>0) {
			$q  = "
				select 
					r.name
					,r.description
					,r.parent_id
				from 
					roles as r
				where 
					r.role_id=".$db->escape($id)."
			";
			$result = $db->query($q);
			if ($result->count()>0) {
				$row = $result[0];
				$name	 			= $row->name;
				$description 		= $row->description;
				
				$parent_id = $row->parent_id;
				
			}
		}
		
		$html= '';
        
		
		$widget = $app->add_widget()->set_icon($icon)->set_title($title);
		
		
		$form = $widget->add_form();
		
		$q = 'select role_id,name from roles where status>0';
		if(strlen($org_id)>0) {
			$q.= ' and org_id='.$db->escape($org_id);
		}
		if(strlen($id)>0) {
			$q.= ' and role_id<>'.$db->escape($id);
		}
		$parent_list =  cdbutils::get_list($q);
		
		
		
		$form->add_field()->set_label('Parent')->add_control('parent_id','select')->set_value($parent_id)->set_list($parent_list);
		$form->add_field()->set_label('Name')->add_control('name','text')->add_validation('required')->set_value($name);
		$form->add_field()->set_label('Description')->add_control('description','textarea')->add_validation(null)->set_value($description);
		$form->add_control('id','hidden')->add_validation(null)->set_value($id);
		$actions = $form->add_action_list();
		$actions->set_style('form-action');
		
		
		$act_next = $actions->add_action()->set_label('Submit')->set_submit(true);
		
		
		echo $app->render();
	}
	public function delete($org_id,$id="") {
		if (strlen($id)==0) {
			curl::redirect('admin/roles');
		}
		$app = CApp::instance();
		$session = Session::instance();
		$db = CDatabase::instance();
		$q = '';
		$error =0; 
		$role = crole::get($id);
		
		
		if($error==0) {
			if($role->is_base==1) {
				$error++;
				$error_message="Fail on delete, data is required by system...";
			}
		}
		if ($error==0) {
			try {    
				$db->update("roles",array("status" => 0,"updated" => date("Y-m-d H:i:s"),"updatedby" => 'administrator'), array("role_id" => $id));
				$tree = CTreeDB::factory('roles');
				$tree->rebuild_tree();
			} catch (Kohana_Exception $e) {
				$error++;
				$error_message = "Fail on delete, please call the administrator...";
			}
		}
        
		if ($error==0) {
		  cmsg::add('success',"Role \"".$role->name."\" Successfully Deleted !");
		} else {
		  //proses gagal
		  cmsg::add('error',$error_message);
		}
		curl::redirect('admin/roles?org_id='.$org_id);
	}
	
	private function update_recursive($data,$parent_id) {
		$db = CDatabase::instance();
		$app = CApp::instance();
		$org = $app->org();
		$user = $app->user();
		$org_id = $org->org_id;
		
		foreach($data as $d) {
			$id = $d['id'];
			
			$data_updated['parent_id'] = $parent_id;
			
			$db->update('roles',$data_updated,array('role_id'=>$id));
			$children = array();
			if(isset($d['children'])) {
				$this->update_recursive($d['children'],$id);
			}
		}
	}
	public function ordering($org_id,$id="") {
		$app = CApp::instance();
		$app->title(clang::__("Roles"));
		$app->add_breadcrumb(clang::__("Roles"), curl::base() . "roles");
		$user = $app->user();
		$role = $app->role();
		$app_role_id = $role->role_id;
		$org = $app->org();
		$org_id = $org->org_id;
		$db = CDatabase::instance();
		$tree = CTreeDB::factory('roles',$org_id);
		$post = $_POST;
		if($post!=null) {
			$error = 0;
			$data = $post['data_order'];
			$data = cjson::decode($data);
			try {
				$db->begin();
				$this->update_recursive($data,null);
				$q = "select * from roles where org_id=".$db->escape($org_id)." and parent_id is null";
				$r = $db->query($q);
				$left=1;
				foreach($r as $row) {
					$tree->rebuild_tree($row->role_id,$left);
					$left = cdbutils::get_value('select rgt from roles where role_id='.$db->escape($row->role_id))+1;
					
				}
			 } catch (Kohana_Exception $e) {
                $error++;
                $error_message = clang::__("system_update_fail") . $e->getMessage();
            }
			if ($error == 0) {
				$db->commit();
				cmsg::add('success', clang::__("Role Order") .clang::__(" "). clang::__("Successfully Modified") . " !");
				clog::activity($user->user_id, 'edit', clang::__("Role Order").clang::__(" ").clang::__("Successfully Modified") . " !");
				curl::redirect('roles');
			} else {
				//proses gagal
				$db->rollback();
				cmsg::add('error', $error_message);
			}
		}
		
		$widget = $app->add_widget()->set_title(clang::__('Change Order'));
		$nestable = $widget->add_nestable();
		$widget->clear_both();
		$nestable->set_data_from_treedb($tree)->set_id_key('role_id')->set_value_key('name')->set_input('data_order');

		$form = $widget->add_form();
		$form->add_control('data_order','hidden')->set_value('');
		$actions = $form->add_action_list()->set_style('form-action');
		$actions->add_action()->set_label('Submit')->set_submit(true);
		echo $app->render();
	}

}