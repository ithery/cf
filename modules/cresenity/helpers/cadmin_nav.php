<?php
class cadmin_nav {
	
	public function nav($nav=null,$controller=null,$method=null) {
		if($nav==null) $nav = CNavigation::instance()->admin_navs();
		if($controller==null) $controller=crouter::controller();
		if($method==null) $method=crouter::method();
		
		if(isset($nav["controller"])&&isset($nav["method"])&&$nav["controller"]==$controller&&$nav["method"]==$method) {
			return $nav;
		}
		if(isset($nav["action"])) {
			foreach($nav["action"] as $act) {
				if(isset($act["controller"])&&isset($act["method"])&&$act["controller"]==$controller&&$act["method"]==$method) {
					return $nav;
				}
			}
		}
		if(isset($nav["subnav"])) {
			foreach($nav["subnav"] as $sn) {
				$res = cadmin_nav::nav($sn,$controller,$method);
				if($res!==false) return $res;
			}
		}
		return false;
	}
	
	
	public static function child_count($nav) {
		if(isset($nav["subnav"])) {
			if(is_array($nav["subnav"])) {
				return count($nav["subnav"]);
			}
		}
		return 0;
	}
	
	public static function have_child($nav) {
		return cadmin_nav::child_count()>0;
	}
	public static function is_leaf($nav) {
		return isset($nav["subnav"])&&is_array($nav["subnav"]);
	}
	public static function url($nav) {
		$controller = "";
		$method = "";
		if(isset($nav["controller"])) $controller = $nav["controller"];
		if(isset($nav["method"])) $method = $nav["method"];
		
		if (strlen($controller)==0) return "";
		if (strlen($method)==0) return "";
		return curl::base()."admin/".$controller."/".$method; 
   
	}
	public function render($navs=null,$level=0,&$child=0) {
		if($navs==null) $navs = CNavigation::instance()->admin_navs();
		$html = "";
		$child_count = 0;
		foreach($navs as $d) {
			$child = 0;
			$pass = 0;
			$active_class = "";
			$controller = "";
			$method = "";
			$label = "";
			$icon = "";
			if(isset($d["controller"])) $controller = $d["controller"];
			if(isset($d["method"])) $method = $d["method"];
			if(isset($d["label"])) $label = $d["label"];
			if(isset($d["icon"])) $icon = $d["icon"];
						
			
			$child_html = "";
			if (isset($d["subnav"])) {
				$child_html .= cadmin_nav::render($d["subnav"],$level+1,$child);
			}
			$url = cadmin_nav::url($d);
			if (!isset($url)||$url==null) $url = "";
			if(strlen($child_html)>0||strlen($url)>0) {
				
				$child_count++;
				
				
				
				$find_nav = cadmin_nav::nav($d);
				if ($find_nav!==false) {
					$active_class = " active";
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
				
				
				$html.='<li class="'.$li_class.$active_class.'">';
				$icon_html = "";
				if(isset($d["icon"])&&strlen($d["icon"])>0) {
					$icon_html = '<i class="icon-'.$d["icon"].'"></i>';
				}
				if($url=="") {
					$elem = '<a class="'.$active_class.' dropdown-toggle" href="javascript:;" data-toggle="dropdown">'.$icon_html.'<span>'.clang::__($label).'</span><b class="caret"></b>';
					if($child>0) {
						//$elem .= '<span class="label">'.$child.'</span>';
					
					} 
					$elem.= "</a>\r\n";
					
				} else {
					$elem = '<a class="'.$active_class.'" href="'.$url.'">'.$icon_html.'<span>'.clang::__($label)."</span></a>\r\n"; 
				}
				$html.=$elem;
				$html.=$child_html;
				$html.='</li>';
			}
		}
		if(strlen($html)>0) {
			if($level==0) {
				$html = "  <ul class=\"mainnav\">\r\n".$html."  </ul>\r\n";
			
			} else {
				$html = "  <ul class=\"dropdown-menu\">\r\n".$html."  </ul>\r\n";
			
			}
		}
		if ($child_count==0) {$html="";}
		$child = $child_count;

		return $html;
		
	}
	
} 

?>