<?php
class cmenu {
	public static function get($id) {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and menu_id = '".$id."'";
		$result = $db->query($query);
		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;

	}
	
	public static function get_first_child($id) {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and parent_id = '".$id."' order by seqno asc";
		$result = $db->query($query);
		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;

	}
	
	public static function get_by_name($name) {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and name = '".$name."'";
		$result = $db->query($query);
		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;

	}
	
	public static function get_by_controller($controller) {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and controller = '".$controller."'";
		$result = $db->query($query);
		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;

	}
	
	public static function get_by_controller_method($controller,$method) {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and controller = '".$controller."' and method='".$method."'";
		$result = $db->query($query);
		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;

	}
	
	public static function child_count($menu_id,$role_id="") {
		$db = CDatabase::instance();
		if(strlen($role_id)>0) {
			$q = "select count(*) as cnt from menu m inner join menu_role as mr on m.menu_id=mr.menu_id where m.status>0 and mr.status>0 and mr.role_id='".$role_id."' and m.parent_id='".($menu_id)."'";
		
		} else {
			$q = "select count(*) as cnt from menu as m where m.status>0 and m.parent_id='".($menu_id)."'";
		}
		$r = $db->query($q);
		$cnt = 0;
		if ($r->count()>0) {
			$cnt = $r[0]->cnt;
		}
		return $cnt;
	}
	 
	public static function is_leaf($menu_id) {
		return cmenu::child_count($menu_id) == 0;
    }
	public static function get_icon($menu_name) {
		$db = CDatabase::instance();
  		
		switch(strtolower($menu_name)) {
			case "dashboard": return "dashboard";
			case "account": return "messages";
			case "prepaid_mobile": return "settings";
			case "configuration": return "stats";
			default: return "dashboard";
		}
		return "dashboard";
	}
	
	public static function topmenu() {
		$db = CDatabase::instance();
  		$query = "select * " .
				 "from menu " .
				 "where status > 0 and parent_id is null order by seqno";
		$result = $db->query($query);
		return $result;
	}
	
	public static function get_topmenu($menu_id) {
		$db = CDatabase::instance();
		$q = "select parent_id from menu where menu_id='".$menu_id."'";
		$r = $db->query($q);
		if($r->count()>0) {
			return cmenu::get_topmenu($r[0]->parent_id);
		} 
		return $menu_id;
	}
	
	public static function is_child($parent_id,$menu_id) {
		$db = CDatabase::instance();
		$q = "select count(*) as cnt from menu where menu_id=".$db->escape($menu_id)." and parent_id=".$db->escape($parent_id).";";
		$r = $db->query($q);
		$cnt=0;
		if($r->count()>0) {
			$cnt = $r[0]->cnt;
		}
		return $cnt>0;
	}

	
	
	public static function delete($menu_id,$db=null) {
		if($db==null) $db=CDatabase::instance();
  		$error=0;

		$q = "select menu_id from menu where parent_id='".$menu_id."'";
		$r = $db->query($q);
		foreach($r as $row) {
			$error = cmenu::delete($db,$row->menu_id);
			if ($error>0) {
				break;
			}
		}
        if ($error==0) {
			try {
			  $db->delete("menu_role",array("menu_id"=>$menu_id));
			  $db->delete("menu",array("menu_id"=>$menu_id));
			}catch(Exception $ex) {
			  $error++;
			  CF::log("error", $ex->getMessage());
			}
        }
		return $error;
	}
	
	
	public static function app_url($controller,$method) {
      if (strlen($controller)==0) return "";
      if (strlen($method)==0) return "";
      return curl::base()."".$controller."/".$method; 
    }
   
	public static function is_menu_available($menu_id,$role_id,$db=null) {
		if ($db==null) $db= CDatabase::instance();
		$q = "select * from menu_role where status>0 and role_id='".$role_id."' and menu_id=".$menu_id;
		$r = $db->query($q);
		//echo $q;
		if($r->count()>0) {
			return true;
		}
		return false;
	}
	public static function populate_menu_user_rights_as_array($app_id,$role_id,$menu_id="",$level=0) {
		$db = CDatabase::instance();
		$count=0;
		$html = "";
		$q ="". 
			" select ".
			"   m.menu_id".
			"   ,m.have_add".
			"   ,m.have_edit".
			"   ,m.have_delete".
			"   ,m.have_confirm".
			"   ,m.have_download".
			"   ,m.caption".
			"   ,m.have_add".
			"   ,mr.menu_role_id".
			"   ,mr.role_id".
			"   ,m.name".
			"   ,m.controller".
			"   ,m.method".
			"   ,mr.can_add".
			"   ,mr.can_edit".
			"   ,mr.can_delete".
			"   ,mr.can_confirm".
			"   ,mr.can_download".
			"   ,mr.status".
			"   ,mr.status as can_access".
			"  from menu as m ".
			"  left join (select * from menu_role as mr where mr.role_id='".$role_id."') as mr on m.menu_id=mr.menu_id ";
			
		$q = $q." where m.status>0 ";
		if(strlen($menu_id)>0) {
			$q.=" and m.parent_id='".($menu_id)."'";
		} else  {
			$q.=" and (m.parent_id is null)";
		}
		if(strlen($app_id)>0) {
			$q.=" and m.app_id='".$app_id."' order by m.seqno asc";
		}			
		$r = $db->query($q);
		$result = array();
		foreach ($r as $row) {
			$d = array();
			$pass = 0;
			$url = cmenu::app_url($row->controller,$row->method);
			if (!isset($url)||$url==null) $url = "";
			$session = Session::instance();
			
			$pass=1;
			if ($pass == 1 ) {
				$count++;
				if($url=="") {
					$url = curl::base()."home/menu/?q=".$row->name;
				}
				$temp_html = array();
				if (!cmenu::is_leaf($row->menu_id)) {
					$temp_html = cmenu::populate_menu_user_rights_as_array($app_id,$role_id,$row->menu_id,$level+1);
			
				} 
				if(count($temp_html)>0||cmenu::is_leaf($row->menu_id)) {
					$d["menu_id"]=$row->menu_id;
					$d["level"]=$level;
					$d["url"]=$url;
					$d["caption"]=$row->caption;
					$d["name"]=$row->name;
					$d["controller"]=$row->controller;
					$d["method"]=$row->method;
					$d["have_add"]=$row->have_add;
					$d["have_edit"]=$row->have_edit;
					$d["have_download"]=$row->have_download;
					$d["have_delete"]=$row->have_delete;
					$d["have_confirm"]=$row->have_confirm;
					$d["can_add"]=$row->can_add;
					$d["can_edit"]=$row->can_edit;
					$d["can_download"]=$row->can_download;
					$d["can_delete"]=$row->can_delete;
					$d["can_confirm"]=$row->can_confirm;
					$d["can_access"]=$row->can_access;
					$d["menu_role_id"]=$row->menu_role_id;
					
					$result[] = $d;
					$result = array_merge($result,$temp_html);
					
				}
				
			}
		}
		
		return $result;
	}
	
	public static function populate_menu_as_array($app_id="",$role_id="",$menu_id="",$level=0) {
		$db = CDatabase::instance();
		$count=0;
		$html = "";
		$q = "select m.menu_id, m.name,m.caption, m.controller, m.method, m.seqno from menu as m where status>0 ";
		
		if(strlen($menu_id)>0) {
			$q.=" and parent_id='".($menu_id)."'";
		} else  {
			$q.=" and (parent_id is null)";
		}
		if(strlen($app_id)>0) {
			$q.=" and app_id='".$app_id."' order by seqno asc";
		}			
		$r = $db->query($q);
		$result = array();
		foreach ($r as $row) {
			$d = array();
			$pass = 0;
			$url = cmenu::app_url($row->controller,$row->method);
			if (!isset($url)||$url==null) $url = "";
			$session = Session::instance();
			if(strlen($role_id)>0) {
				if (cmenu::is_menu_available($row->menu_id,$role_id,$db)||(!cmenu::is_leaf($row->menu_id))) {
					$pass=1;
				}
			} else {
				$pass=1;
			}
			//$pass=1;
			if ($pass == 1 ) {
				$count++;
				if($url=="") {
					$url = curl::base()."home/menu/?q=".$row->name;
				}
				$temp_html = array();
				if (!cmenu::is_leaf($row->menu_id)) {
					$temp_html = cmenu::populate_menu_as_array($app_id,$role_id,$row->menu_id,$level+1);

				} 
				if(count($temp_html)>0||cmenu::is_leaf($row->menu_id)) {
					$d["menu_id"]=$row->menu_id;
					$d["level"]=$level;
					$d["url"]=$url;
					$d["caption"]=$row->caption;
					$d["name"]=$row->name;
					$d["controller"]=$row->controller;
					$d["method"]=$row->method;
					
					$result[] = $d;
					$result = array_merge($result,$temp_html);
				}
				
			}
		}
		
		return $result;
	}
	
    public static $last_recursive_count = 0;
    
    public static function populate_menu($role_id="",$menu_id="",$level=0,&$child=0) {
		$db = CDatabase::instance();
		$count=0;
		$html = "";
		if(strlen($menu_id)==0) {
			$q = "select menu_id, name,caption, controller, method, seqno, icon from menu where status>0 and parent_id is null order by seqno asc";
		} else {
			$q = "select menu_id, name,caption, controller, method, seqno, icon from menu where status>0 and parent_id='".($menu_id)."' order by seqno asc";
		}

		$r = $db->query($q);
		$child_count=0;
		
		foreach ($r as $row) {
			$child = 0;
			$pass = 0;
			$url = cmenu::app_url($row->controller,$row->method);
			if (!isset($url)||$url==null) $url = "";
			$session = Session::instance();
			$icon = $row->icon;
			if (cmenu::is_menu_available($row->menu_id,$role_id,$db)||(!cmenu::is_leaf($row->menu_id))) {
				$pass=1;
			}
			//$pass=1;
			if ($pass == 1 ) {
				$count++;
				if($url=="") {
					//$url = curl::base()."home/menu/?q=".$row->name;
				}
				$temp_html = "";
				if (!cmenu::is_leaf($row->menu_id)) {
					$temp_html .= cmenu::populate_menu($role_id,$row->menu_id,$level+1,$child);
				} 
				//count child count
				//$child_count = cmenu::child_count($row->menu_id,$role_id);
				if(strlen($temp_html)>0||cmenu::is_leaf($row->menu_id)) {
					$child_count++;
					$current = "";
					if(Router::$controller=="home"&&Router::$method=="menu") {
						$menu = null;
						$menu_child = null;
						if(isset($_GET["q"])) {
							$menu = cmenu::get_by_name($_GET["q"]);
							
							if($menu!=null) {
								$menu_child=menu::get_first_child($menu->menu_id);
								
							}
						}
						if($menu_child!=null) {
							if($menu_child->menu_id==$row->menu_id) {
								$current=" active";
							}
						}
					}
					if(strlen($current)==0) {
						$menu = cmenu::get_by_controller_method(Router::$controller,Router::$method);
						
						if($menu!=null) {
							if($menu->menu_id==$row->menu_id||cmenu::is_child($row->menu_id,$menu->menu_id)) {
								$current=" active";
							}
							
						}
					}
					if(strlen($current)==0) {
						if(Router::$method=="add"||Router::$method=="edit"||Router::$method=="index") {
							$menu = cmenu::get_by_controller(Router::$controller);
							
							if($menu!=null) {
								if($menu->menu_id==$row->menu_id||cmenu::is_child($row->menu_id,$menu->menu_id)) {
									$current=" active";
								}
								
							}
						}
					}
					if(strlen($current)==0) {
						$menu = cmenu::get_by_controller_method(Router::$controller,Router::$method);
						if($menu!=null) {
							if($menu->menu_id==$row->menu_id||cmenu::is_child($row->menu_id,$menu->menu_id)) {
								$current=" active";
							}
						}
					}
					if(strlen($current)>0) {
						if(cmenu::is_child($row->menu_id,$menu->menu_id)&&$url=="") {
							$current=" active";
						}
					}
					$li_class = "";
					if($child>0) {
						$li_class.=" with-right-arrow";
						if($level==0) {
							$li_class.=" dropdown";
						
						} else {
							$li_class.=" dropdown-submenu";
						
						}
					}
					if($url=="") {
						//$li_class.=" submenu";
					
					}
					$html .= cutils::indent($level);
					$html .= '<li class="'.$li_class.$current.'">';
					$iconhtml = "";
					if(strlen($icon)>0) {
						$iconhtml = '<i class="icon-'.$icon.'"></i>';
					}
					if($url=="") {
						
						$elem = '<a class="'.$current.' dropdown-toggle" href="javascript:;" data-toggle="dropdown">'.$iconhtml.'<span>'.clang::__($row->caption).'</span><b class="caret"></b>';
						if($child>0) {
							//$elem .= '<span class="label">'.$child.'</span>';
						
						} 
						$elem.= "</a>\r\n"; 
					} else {
						$elem = '<a class="'.$current.'" href="'.$url.'">'.$iconhtml.'<span>'.clang::__($row->caption)."</span></a>\r\n"; 
					}
					
					$html.=$elem;

					$html .= cutils::indent($level);
					if (!cmenu::is_leaf($row->menu_id)) {
						$html .= $temp_html;
						$html .= cutils::indent($level);
					}
					$html .= cutils::indent($level);
					$html .= "</li>\r\n";
				}
			  
			}
		}
		if(strlen($html)>0) {
			if($level==0) {
				$html = "  <ul class=\"mainnav\">\r\n".$html."  </ul>\r\n";
			
			} else {
				$html = "  <ul class=\"dropdown-menu\">\r\n".$html."  </ul>\r\n";
			
			}
		}
		if ($count==0) {$html="";}
		$child = $child_count;
		return $html;
	}  
		
		
	public static function populate_menu_tree($menu_id="") {
		function _populate_menu_tree_recursive($db,$menu_id,$menu_name,$menu_url,$level) {
		  $count=0;
		  $html = "";
		  $q = "select menu_id, name,caption, controller, method, seqno from menu where status>0 and parent_id='".($menu_id)."' order by seqno asc";
		  $r = $db->query($q);
		  
		  $html .= "  <ul>\r\n";
		  $total_row = $r->count();
		  foreach ($r as $row) {
			$pass = 0;
			$url = cmenu::app_url($row->controller,$row->method);
			if (!isset($url)||$url==null) $url = "";
			$session = Session::instance();
			$acceptable_url = $session->get("acceptable_url");
			if (isset($acceptable_url)&&$acceptable_url != null) {         
			  if (strlen($url)==0||in_array($url,$acceptable_url)) {
				$pass = 1; 
			  }
			}
			$pass=1;
			if ($pass == 1 ) {
			  if($url=="") {
			    $url = curl::base()."home/menu/?q=".$row->name;
			  }
			  $count++;
			  $class ="";
			  if ($total_row==$count) $class=" class=\"last\"";
			  $html .= cutils::indent($level);
			  $html .= "<li".$class."><a href=\"".$url."\">".$row->caption."</a>\r\n"; 
			  $html .= cutils::indent($level);
			  if (!cmenu::is_leaf($row->menu_id)) {
				$html .= _populate_menu_tree_recursive($db, $row->menu_id,$row->name,$url,$level+1);
				$html .= cutils::indent($level);
			  }
			  $html .= cutils::indent($level);
			  $html .= "</li>\r\n";
			  
			}
		  }
		  $html .= "  </ul>\r\n";
		  if ($count==0) {$html="";}
		  return $html;
		}  
		$db = new Database;
		$html = "";
		$html .= "
        <!-- suckerfish-->\r\n
                <div class=\"tree\">\r\n
                <ul>\r\n";
		$session = Session::instance(); 
		$user = $session->get("user");
		if (isset($user)&&is_object($user)) {
			$q = "";
			if ($menu_id=="") {
			$q = "select menu_id, name,caption,  controller, method, seqno from menu where status>0 and parent_id is null and app_id=1 order by seqno asc";
			} else {
			$q = "select menu_id, name,caption,  controller, method, seqno from menu where status>0 and parent_id = '".$menu_id."' and app_id=1 order by seqno asc";
			}
			$r = $db->query($q);
			$level = 8;

			$acceptable_url = $session->get("acceptable_url");
			foreach($r as $row) {
				$pass = 0;
				$url = cmenu::app_url($row->controller,$row->method);
				if (!isset($url)||$url==null) $url = "";
				if (isset($acceptable_url) && $acceptable_url != null) {

					if (strlen($url)==0 || in_array($url,$acceptable_url)) {
						$pass = 1;
					}
				}
				if (($user->role_id==app_const::$superadmin_id)) {
					$pass=1;
				}
				if ((strtoupper($row->name)=="DEVELOPER") && ($user->role_id!=app_const::$superadmin_id)&&app_const::$developer=0) {
					$pass=0;
				}
				if ($pass == 1 ) {
					if($url=="") {
						$url = curl::base()."home/menu/?q=".$row->name;
					}
					$html .= cutils::indent($level);
					$html .= "<li><strong><a href=\"".$url."\">".$row->caption."</a></strong>\r\n";
					$html .= cutils::indent($level);
					$html .= _populate_menu_tree_recursive($db, $row->menu_id, $row->name, $url, $level+1);
					$html .= cutils::indent($level);
					$html .= "</li>\r\n";            
				}
			}
               
		}
      
		$html .= "
				</ul>\r\n
				</div>\r\n
		<!-- end suckerfish -->\r\n
		";      
		return $html;
	  
    } 
	
} 

?>