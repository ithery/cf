<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 1, 2018, 12:11:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Navigation_Helper {

    protected static $role_navs = array();

    public static function nav($nav = null, $controller = null, $method = null, $path = null) {
        if ($controller == null)
            $controller = crouter::controller();
        if ($method == null)
            $method = crouter::method();
        if ($path == null)
            $path = CFRouter::$controller_dir;


        if ($nav == null) {
            $navs = CApp_Navigation_Data::get();
            if ($navs == null)
                return null;
            foreach ($navs as $nav) {
                $res = self::nav($nav, $controller, $method);
                if ($res !== false)
                    return $res;
            }
        } else {
            $nav_path = carr::get($nav, 'path', '');
            $nav_method = carr::get($nav, 'method', '');
            $nav_controller = carr::get($nav, 'controller', '');

            if ($nav_controller != '' && $nav_method != '' && $path . $controller == $nav_path . $nav_controller && $method == $nav_method) {
                return $nav;
            }

//                var_dump($path .$controller);
//                cdbg::var_dump($nav);


            if (isset($nav["action"])) {
                foreach ($nav["action"] as $act) {
                    $act_path = carr::get($act, 'path', $nav_path);
                    $act_method = carr::get($act, 'method', $nav_method);
                    $act_controller = carr::get($act, 'controller', $nav_controller);
                    if ($act_controller != '' && $act_method != '' && $path . $controller == $act_path . $act_controller && $method == $act_method) {
                        return $nav;
                    }
                }
            }
            if (isset($nav["subnav"])) {
                foreach ($nav["subnav"] as $sn) {
                    $res = self::nav($sn, $controller, $method);
                    if ($res !== false)
                        return $res;
                }
            }
        }
        return false;
    }

    public static function haveAccess($nav = null, $roleId = null, $appId = null, $domain = null) {

        $app = CApp::instance();
        if ($roleId == null) {
            $role = $app->role();
            if ($role != null)
                $roleId = $role->role_id;
        }
        if ($appId == null) {
            $appId = CF::appId();
        }
        if ($nav == null)
            $nav = self::nav();

        if ($nav === false) {
            return false;
        }
        if (isset($_COOKIE['capp-administrator'])) {
            return true;
        }
        $db = CDatabase::instance($domain);
        if ($roleId == "PUBLIC") {
            $roleId = null;
        }

        $role = crole::get($roleId);
        if ($role != null) {
            if ($role->parent_id == null)
                return true;
        }


        if (!isset(self::$role_navs[$appId]) || !isset(self::$role_navs[$appId][$roleId])) {
            if (!isset(self::$role_navs[$appId])) {
                self::$role_navs[$appId] = array();
            }
            if (!isset(self::$role_navs[$appId][$roleId])) {

                $roleNavModel = CApp::model('RoleNav')->whereNull('role_id');
                //$q = "select nav from role_nav where role_id is null and app_id=" . $db->escape($appId);
                if ($roleId != null) {
                    $roleNavModel = CApp::model('RoleNav')->where('role_id', '=', $roleId);
                    //$q = "select nav from role_nav where role_id=" . $db->escape($roleId) . " and app_id=" . $db->escape($appId);
                }
                self::$role_navs[$appId][$roleId] = $roleNavModel->where('app_id', '=', $appId)->get()->pluck('nav')->toArray();
            }
        }

        return in_array($nav["name"], self::$role_navs[$appId][$roleId]);
    }

    public static function havePermission($action, $nav = null, $roleId = null, $appId = null, $domain = null) {


        $app = CApp::instance();

        if ($roleId == null) {
            $role = $app->role();
            if ($role == null) {
                return false;
            }
            $roleId = $role->role_id;
        }
        if ($appId == null) {
            $appId = $app->appId();
        }
        $db = CDatabase::instance($domain);

        /* @var $role CApp_Model */
        $role = CApp::model('Roles')->find($roleId);

        if ($role != null && $role->parent_id == null) {
            return true;
        }


        $db = CDatabase::instance($domain);

        return $role->rolePermission()->where('name', '=', $action)->where('app_id', '=', $appId)->count() > 0;
    }

    public static function appUserRightsArray($appId, $roleId, $app_role_id = "", $domain = null) {
        $navs = CApp_Navigation_Data::get($domain);
        return self::asUserRightsArray($appId, $roleId, $navs, $app_role_id, $domain);
    }

    public static function asUserRightsArray($appId, $roleId, $navs = null, $app_role_id = "", $domain = "", $level = 0) {
        if ($navs == null)
            $navs = CNavigation::instance()->navs();



        $result = array();




        foreach ($navs as $d) {
            if (!self::accessAvailable($d, $appId, $domain, $app_role_id)) {
                continue;
            }

            $res = $d;
            $res["level"] = $level;
            $res["role_id"] = $roleId;
            $res["app_id"] = $appId;
            $res["domain"] = $domain;
            $subnav = array();
            if (isset($d["subnav"]) && is_array($d["subnav"]) && count($d["subnav"]) > 0) {
                $subnav = self::asUserRightsArray($appId, $roleId, $d["subnav"], $app_role_id, $domain, $level + 1);
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

    public static function isPublic($nav) {
        if (isset($nav["is_public"]) && $nav["is_public"]) {
            return true;
        }
        return false;
    }

    public static function childCount($nav) {
        if (isset($nav["subnav"])) {
            if (is_array($nav["subnav"])) {
                return count($nav["subnav"]);
            }
        }
        return 0;
    }

    public static function haveChild($nav) {
        return self::childCount() > 0;
    }

    public static function isLeaf($nav) {
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


        return $url;
    }

    public static function accessAvailable($nav = null, $appId = "", $domain = "", $app_role_id = "") {
        if ($nav == null)
            $nav = self::nav();
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
                    if (!self::haveAccess($nav, $app_role_id, $appId)) {

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

    public static function permissionAvailable($action, $nav = null, $appId = "", $domain = "", $app_role_id = "") {

        if ($nav == null)
            $nav = self::nav();
        if ($nav === false)
            return false;

        if (!self::accessAvailable($nav, $appId, $domain, $app_role_id))
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

    public static function render($navs = null, $level = 0, &$child = 0) {
        $is_admin = CApp::instance()->isAdmin();
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
                $child_html .= self::render($d["subnav"], $level + 1, $child);
            }

            $url = self::url($d);

            if (!isset($url) || $url == null)
                $url = "";

            if (strlen($child_html) > 0 || strlen($url) > 0) {
                if (!self::accessAvailable($d, CF::app_id(), CF::domain())) {
                    continue;
                }
                if (isset($d["controller"]) && $d["controller"] != "") {
                    if (!$is_admin && ccfg::get("have_user_access")) {

                        if (!self::haveAccess($d)) {
                            continue;
                        }
                    }
                }

                $child_count++;

                $border = carr::get($d, 'border');

                $find_nav = self::nav($d);

                if ($find_nav !== false) {
                    $active_class = " active";
                }

                $li_class = "sidenav-item ";
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

                $iconClass = carr::get($d, 'icon');
                if (strlen($iconClass) > 0 && strpos($iconClass, 'fa-') === false && strpos($iconClass, 'ion-') === false) {
                    $iconClass = 'icon-' . $iconClass;
                }
                $icon_html = "";
                if (strlen($iconClass) > 0) {
                    $icon_html = '<i class="' . $iconClass . '"></i>';
                }
                if ($url == "") {
                    $caret = "";
                    if ($level == 0) {
                        $caret = '<b class="caret">';
                    }

                    $elem = '<a class="' . $active_class . ' dropdown-toggle sidenav-link sidenav-toggle" href="javascript:;" data-toggle="dropdown">' . $icon_html . '<span>' . clang::__($label) . '</span>' . $caret . '</b>';
                    if ($child > 0) {
                        //$elem .= '<span class="label">'.$child.'</span>';
                    }
                    $elem .= "</a>\r\n";
                } else {
                    $target = "";
                    $notif = "";
                    if (isset($d["target"]) && strlen($d["target"]) > 0) {
                        $target = ' target="' . $d["target"] . '"';
                    }
                    if (isset($d["notif_count"])) {
                        $callable = $d["notif_count"];

                        if (is_callable($callable)) {
                            $notif = call_user_func($callable);
                        }
                    }

                    $strNotif = '';
                    if ($notif != null && $notif > 0) {
                        $strNotif = ' <span class="label label-info nav-notif nav-notif-count">' . $notif . '</span>';
                    }
                    $elem = '<a class="' . $active_class . ' sidenav-link" href="' . $url . '"' . $target . '>' . $icon_html . '<span>' . clang::__($label) . "</span>" . $strNotif . "</a>\r\n";
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

}
