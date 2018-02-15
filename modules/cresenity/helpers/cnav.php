<?php

class cnav {

    protected static $role_navs = array();

    public static function nav($nav = null, $controller = null, $method = null, $path = null) {
        if ($controller == null)
            $controller = crouter::controller();
        if ($method == null)
            $method = crouter::method();
        if ($path == null)
            $path = crouter::controller_dir();


        if ($nav == null) {
            $navs = CApp::nav()->getArray();
            if ($navs == null)
                return null;
            foreach ($navs as $nav) {
                $res = cnav::nav($nav, $controller, $method);
                if ($res !== false)
                    return $res;
            }
        } else {
            $nav_path = carr::get($nav, 'path', '');
            $nav_method = carr::get($nav, 'method', '');
            $nav_controller = carr::get($nav, 'controller', '');

//                var_dump($path .$controller);
//                cdbg::var_dump($nav);

            if ($nav_path != '') {
                if ($nav_controller != '' && $nav_method != '' && $nav_controller == $controller && $nav_method == $method) {
                    return $nav;
                }
            } else {
                if ($nav_controller != '' && $nav_method != '' && $nav_controller == $path . $controller && $nav_method == $method) {
                    return $nav;
                }
            }
            if (isset($nav["action"])) {
                foreach ($nav["action"] as $act) {
                    $act_path = carr::get($nav, 'path', $nav_path);
                    $act_method = carr::get($nav, 'method', $nav_method);
                    $act_controller = carr::get($nav, 'controller', $nav_controller);
                    if ($act_controller != '' && $act_method != '' && $act_controller == $controller && $act_method == $method && $act_path == $path) {
                        return $nav;
                    }
                }
            }
            if (isset($nav["subnav"])) {
                foreach ($nav["subnav"] as $sn) {
                    $res = cnav::nav($sn, $controller, $method);
                    if ($res !== false)
                        return $res;
                }
            }
        }
        return false;
    }

    public static function have_access($nav = null, $role_id = null, $app_id = null, $domain = null) {

        $app = CApp::instance();
        if ($role_id == null) {
            $role = $app->role();
            if ($role != null)
                $role_id = $role->role_id;
        }
        if ($app_id == null) {
            $app_id = $app->app_id();
        }
        if ($nav == null)
            $nav = cnav::nav();

        if ($nav === false)
            return false;
        $db = CDatabase::instance($domain);
        if ($role_id == "PUBLIC") {
            $role_id = null;
        }

        $role = crole::get($role_id);
        if ($role != null) {
            if ($role->parent_id == null)
                return true;
        }


        if (!isset(self::$role_navs[$app_id]) || !isset(self::$role_navs[$app_id][$role_id])) {
            if (!isset(self::$role_navs[$app_id])) {
                self::$role_navs[$app_id] = array();
            }
            if (!isset(self::$role_navs[$app_id][$role_id])) {

                $q = "select nav from role_nav where role_id=" . $db->escape($role_id) . " and app_id=" . $db->escape($app_id);
                if ($role_id == null) {
                    $q = "select nav from role_nav where role_id is null and app_id=" . $db->escape($app_id);
                }
                self::$role_navs[$app_id][$role_id] = cdbutils::get_array($q);
            }
        }

        return in_array($nav["name"], self::$role_navs[$app_id][$role_id]);
    }

    public static function have_permission($action, $nav = null, $role_id = null, $app_id = null, $domain = null) {
        $app = CApp::instance();

        if ($role_id == null) {
            $role = $app->role();
            if ($role == null)
                return false;
            $role_id = $role->role_id;
        }
        if ($app_id == null) {
            $app_id = $app->app_id();
        }
        $db = CDatabase::instance($domain);

        $role = cdbutils::get_row('select * from roles where role_id=' . $db->escape($role_id));
        if ($role->parent_id == null)
            return true;


        $db = CDatabase::instance($domain);
        $q = "select * from role_permission where name=" . $db->escape($action) . " and role_id=" . $db->escape($role_id) . " and app_id=" . $db->escape($app_id);
        $r = $db->query($q);

        return $r->count() > 0;


        /*
          if($nav==null) $nav = cnav::nav();

          if($nav===false) return false;

          $navname = $nav;
          if(is_array($navname)) {
          $navname = $nav["name"];
          }


          $q = "select * from role_permission where name=".$db->escape($action)." and nav=".$db->escape($navname)." and role_id=".$db->escape($role_id)." and app_id=".$db->escape($app_id);



          $r = $db->query($q);
          return $r->count()>0;
         */
    }

    public static function app_user_rights_array($app_id, $role_id, $app_role_id = "", $domain = "") {
        $navs = CNavigation::instance($app_id)->navs();
        return cnav::as_user_rights_array($app_id, $role_id, $navs, $app_role_id, $domain);
    }

    public static function as_user_rights_array($app_id, $role_id, $navs = null, $app_role_id = "", $domain = "", $level = 0) {
        if ($navs == null)
            $navs = CNavigation::instance()->navs();



        $result = array();




        foreach ($navs as $d) {
            if (!cnav::access_available($d, $app_id, $domain, $app_role_id)) {
                continue;
            }

            $res = $d;
            $res["level"] = $level;
            $res["role_id"] = $role_id;
            $res["app_id"] = $app_id;
            $res["domain"] = $domain;
            $subnav = array();
            if (isset($d["subnav"]) && is_array($d["subnav"]) && count($d["subnav"]) > 0) {
                $subnav = cnav::as_user_rights_array($app_id, $role_id, $d["subnav"], $app_role_id, $domain, $level + 1);
            }
            if (count($subnav) == 0 && (!isset($d["controller"]) || strlen($d["controller"]) == 0 )) {
                if (!isset($d["link"]) || strlen($d["link"]) == 0) {
                    continue;
                }
            }
            $result[] = $res;
            $result = array_merge($result, $subnav);
        }
        return $result;
    }

    public static function is_public($nav) {
        if (isset($nav["is_public"]) && $nav["is_public"]) {
            return true;
        }
        return false;
    }

    public static function child_count($nav) {
        if (isset($nav["subnav"])) {
            if (is_array($nav["subnav"])) {
                return count($nav["subnav"]);
            }
        }
        return 0;
    }

    public static function have_child($nav) {
        return cnav::child_count() > 0;
    }

    public static function is_leaf($nav) {
        return isset($nav["subnav"]) && is_array($nav["subnav"]);
    }

    public static function url($nav) {
        $controller = "";
        $method = "";
        $path = "";
        $link = "";

        if (isset($nav["path"]))
            $path = $nav["path"];
        if (isset($nav["controller"]))
            $controller = $nav["controller"];
        if (isset($nav["method"]))
            $method = $nav["method"];
        if (isset($nav["link"]))
            $link = $nav["link"];

        if (strlen($link) > 0) {
            $url = $link;
        } else {
            if (strlen($path) > 0)
                $path .= '/';
            if (strlen($controller) == 0)
                return "";
            if (strlen($method) == 0)
                return "";
            $url = curl::base() . $path . $controller . "/" . $method;
        }


        if (CApp::instance()->is_admin()) {
            //$url =curl::base().'admin/'.$controller."/".$method; 
        }
        return $url;
    }

    public static function render_theme($theme, $navs = null, $level = 0, &$child = 0, &$activated = false) {
        $is_admin = CApp::instance()->is_admin();
        if ($navs == null)
            $navs = CNavigation::instance()->navs();

        if ($navs == null)
            return false;
        $html = "";
        $child_count = 0;
        $nav_active = false;
        foreach ($navs as $d) {
            if ($level <= 1) {
                $activated = false;
            }
            $child = 0;
            $active_class = "";
            $controller = "";
            $method = "";
            $label = "";
            $icon = "";
            if (isset($d["controller"]))
                $controller = $d["controller"];
            if (isset($d["method"]))
                $method = $d["method"];
            if (isset($d["label"]))
                $label = $d["label"];
            if (isset($d["icon"]))
                $icon = $d["icon"];

            $child_html = "";

            if (isset($d["subnav"])) {
                $child_html .= cnav::render_theme($theme, $d["subnav"], $level + 1, $child, $activated);
            }

            $url = cnav::url($d);
            if (!isset($url) || $url == null) {
                $url = "";
            }

            if (strlen($child_html) > 0 || strlen($url) > 0) {
                if (!cnav::access_available($d, CF::app_id(), CF::domain())) {
                    continue;
                }
                if (isset($d["controller"]) && $d["controller"] != "") {
                    if (!$is_admin && ccfg::get("have_user_access")) {
                        if (!cnav::have_access($d)) {
                            continue;
                        }
                    }
                }

                $child_count++;

                $li_class = "";
                if ($child > 0) {
//                        $li_class.=" with-right-arrow";
                    if ($level == 0) {
                        $li_class .= " treeview";
                    }
                }

                // selected navigator
                $find_nav = cnav::nav($d);
                if ($find_nav !== false || $activated == true) {
                    $nav_active = true;
                    $active_class = " active";
                }

                // icon
                $icon_html = "";
                if (isset($d["icon"]) && strlen($d["icon"]) > 0) {
                    $icon_html = '<i class="fa fa-' . $d["icon"] . '"></i>';
                }

                $html .= "<li class='" . $active_class . " " . $li_class . "'>";

                if ($url == "") {
                    $elem = "<a href='#'>
                                    " . $icon_html . "</i><span>" . clang::__($label) . "</span> 
                                    <i class='fa fa-angle-left pull-right'></i>
                                  </a>";
                } else {
                    $elem = "<a href='" . $url . "'>" . $icon_html . "<span>" . clang::__($label) . "</span></a>";
                }
                $html .= $elem;
                $html .= $child_html;
                $html .= '</li>';
            }
        }

        if ($nav_active == true) {
            $activated = true;
        }

        if (strlen($html) > 0) {
            if ($level == 0) {
                $html = "<ul class='sidebar-menu'>" . $html . "</ul>";
            } else {
                $html = "<ul class=\"treeview-menu\">\r\n" . $html . "  </ul>\r\n";
            }
        }

        if ($child_count == 0) {
            $html = "";
        }
        $child = $child_count;
        return $html;
    }

    public static function render($navs = null, $level = 0, &$child = 0) {
        $is_admin = CApp::instance()->is_admin();
        if ($navs == null)
            $navs = CNavigation::instance()->navs();

        if ($navs == null)
            return false;
        $html = "";
        $child_count = 0;
        foreach ($navs as $d) {

            $child = 0;
            $pass = 0;
            $active_class = "";
            $controller = "";
            $method = "";
            $label = "";
            $icon = "";
            if (isset($d["controller"]))
                $controller = $d["controller"];
            if (isset($d["method"]))
                $method = $d["method"];
            if (isset($d["label"]))
                $label = $d["label"];
            if (isset($d["icon"]))
                $icon = $d["icon"];


            $child_html = "";

            if (isset($d["subnav"])) {
                $child_html .= cnav::render($d["subnav"], $level + 1, $child);
            }

            $url = cnav::url($d);

            if (!isset($url) || $url == null)
                $url = "";

            if (strlen($child_html) > 0 || strlen($url) > 0) {
                if (!cnav::access_available($d, CF::app_id(), CF::domain())) {
                    continue;
                }
                if (isset($d["controller"]) && $d["controller"] != "") {
                    if (!$is_admin && ccfg::get("have_user_access")) {

                        if (!cnav::have_access($d)) {
                            continue;
                        }
                    }
                }

                $child_count++;

                $border = carr::get($d, 'border');

                $find_nav = cnav::nav($d);

                if ($find_nav !== false) {
                    $active_class = " active";
                }

                $li_class = "";
                if ($child > 0) {
                    $li_class .= " with-right-arrow";
                    if ($level == 0) {
                        $li_class .= " dropdown";
                    } else {
                        $li_class .= " dropdown-submenu ";
                    }
                }

                $addition_style = '';
                if ($border == 'top') {
                    $addition_style = ' style="border-top:1px solid #bbb"';
                }
                if ($border == 'bottom') {
                    $addition_style = ' style="border-bottom:1px solid #bbb"';
                }

                $html .= '<li class="' . $li_class . $active_class . '" ' . $addition_style . '>';
                $icon_html = "";
                if (isset($d["icon"]) && strlen($d["icon"]) > 0) {
                    $icon_html = '<i class="icon-' . $d["icon"] . '"></i>';
                }
                if ($url == "") {
                    $caret = "";
                    if ($level == 0) {
                        $caret = '<b class="caret">';
                    }
                    $elem = '<a class="' . $active_class . ' dropdown-toggle " href="javascript:;" data-toggle="dropdown">' . $icon_html . '<span>' . clang::__($label) . '</span>' . $caret . '</b>';
                    if ($child > 0) {
                        //$elem .= '<span class="label">'.$child.'</span>';
                    }
                    $elem .= "</a>\r\n";
                } else {
                    $target = "";
                    if (isset($d["target"]) && strlen($d["target"]) > 0) {
                        $target = ' target="' . $d["target"] . '"';
                    }
                    $elem = '<a class="' . $active_class . '" href="' . $url . '"' . $target . '>' . $icon_html . '<span>' . clang::__($label) . "</span></a>\r\n";
                }
                $html .= $elem;
                $html .= $child_html;
                $html .= '</li>';
            }
        }
        if (strlen($html) > 0) {
            if ($level == 0) {

                $html = "  <ul class=\"mainnav \">\r\n" . $html . "  </ul>\r\n";
            } else {
                $html = "  <ul class=\"dropdown-menu\">\r\n" . $html . "  </ul>\r\n";
            }
        }
        if ($child_count == 0) {
            $html = "";
        }
        $child = $child_count;

        return $html;
    }

    public static function access_available($nav = null, $app_id = "", $domain = "", $app_role_id = "") {
        if ($nav == null)
            $nav = cnav::nav();
        if ($nav === false)
            return false;
        $navname = $nav["name"];
        $app = CApp::instance();

        if (strlen($app_role_id) == 0) {
            if ($app->user() != null) {
                $app_role_id = cobj::get($app->user(), 'role_id');
            }
        }

        if (strlen($app_role_id) > 0) {
            $app_role = crole::get($app_role_id);
            if ($app_role != null && $app_role->parent_id == null)
                return true;
            if ($app_role != null && (!isset($nav["subnav"]) || count($nav["subnav"]) == 0)) {

                $parent_role_id = $app_role->parent_id;
                if ($parent_role_id != null) {
                    if (!cnav::have_access($nav, $app_role_id, $app_id)) {

                        return false;
                    }
                }
            }
        }

        if (isset($nav["requirements"])) {
            $requirements = $nav["requirements"];
            foreach ($requirements as $k => $v) {

                $config_value = ccfg::get($k, $domain);
                if ($config_value != $v) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function permission_available($action, $nav = null, $app_id = "", $domain = "", $app_role_id = "") {

        if ($nav == null)
            $nav = cnav::nav();
        if ($nav === false)
            return false;

        if (!cnav::access_available($nav, $app_id, $domain, $app_role_id))
            return false;

        $navname = $nav["name"];
        if (isset($nav["action"])) {
            $navactions = $nav["action"];
            foreach ($navactions as $act) {
                if ($act['name'] == $action && isset($act["requirements"])) {

                    $requirements = $act["requirements"];

                    foreach ($requirements as $k => $v) {
                        $config_value = ccfg::get($k, $domain);
                        if ($config_value != $v) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public static function report_action($name) {
        return array(
            array(
                'name' => 'download_xls_' . $name . '_report',
                'label' => 'Download XLS',
                'controller' => 'report_' . $name . '',
                'method' => 'download',
                "requirements" => array(
                    "have_report_xls" => true,
                ),
            ),
            array(
                'name' => 'download_xls_xml_' . $name . '_report',
                'label' => 'Download XLS XML',
                'controller' => 'report_' . $name . '',
                'method' => 'download',
                "requirements" => array(
                    "have_report_xls_xml" => true,
                ),
            ),
            array(
                'name' => 'download_csv_' . $name . '_report',
                'label' => 'Download CSV',
                'controller' => 'report_' . $name . '',
                'method' => 'download',
                "requirements" => array(
                    "have_report_csv" => true,
                ),
            ),
            array(
                'name' => 'download_pdf_' . $name . '_report',
                'label' => 'Download PDF',
                'controller' => 'report_' . $name . '',
                'method' => 'download',
                "requirements" => array(
                    "have_report_pdf" => true,
                ),
            ),
        ); //end action report_cashflow
    }

}

?>