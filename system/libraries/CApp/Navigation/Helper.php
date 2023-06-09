<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 1, 2018, 12:11:34 PM
 */
class CApp_Navigation_Helper {
    protected static $role_navs = [];

    /**
     * @param null|mixed $nav
     * @param null|mixed $controller
     * @param null|mixed $method
     * @param null|mixed $path
     *
     * @return array|bool
     */
    public static function nav($nav = null, $controller = null, $method = null, $path = null) {
        $routeData = c::router()->getCurrentRoute()->getRouteData();
        // cdbg::dd($routeData);
        if ($controller == null) {
            $controller = $routeData->getController();
        }
        if ($method == null) {
            $method = $routeData->getMethod();
        }
        if ($path == null) {
            $path = $routeData->getControllerDir();
        }
        if ($nav == null) {
            $navs = CApp_Navigation_Data::get();

            if ($navs == null) {
                return null;
            }

            foreach ($navs as $nav) {
                $res = self::nav($nav, $controller, $method);
                if ($res !== false) {
                    return $res;
                }
            }
        } else {
            $navPath = carr::get($nav, 'path', '');
            $navMethod = carr::get($nav, 'method', '');
            $navController = carr::get($nav, 'controller', '');

            $navAlias = carr::wrap(carr::get($nav, 'alias', ''));
            $navAliases = carr::wrap(carr::get($nav, 'aliases', []));
            $navUri = carr::get($nav, 'uri', '');
            $routerUri = $path . $controller . '/' . $method;

            if ($navUri != null) {
                if (trim($navUri, '/') == trim($routerUri, '/')) {
                    return $nav;
                }
            }

            if (is_array($navAliases)) {
                if (in_array(trim($routerUri, '/'), $navAliases)) {
                    return $nav;
                }
            }

            if ($navController != ''
                && $navMethod != ''
                && $path . $controller == $navPath . $navController
                && ($method == $navMethod || in_array($method, $navAlias))
            ) {
                return $nav;
            }

            if (isset($nav['action'])) {
                foreach ($nav['action'] as $act) {
                    $actPath = carr::get($act, 'path', $navPath);
                    $actMethod = carr::get($act, 'method', $navMethod);
                    $actController = carr::get($act, 'controller', $navController);
                    if ($actController != '' && $actMethod != '' && $path . $controller == $actPath . $actController && $method == $actMethod) {
                        return $nav;
                    }
                }
            }
            if (isset($nav['subnav'])) {
                foreach ($nav['subnav'] as $sn) {
                    $res = self::nav($sn, $controller, $method);
                    if ($res !== false) {
                        return $res;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param null|mixed $nav
     * @param null|mixed $roleId
     * @param null|mixed $appId
     * @param null|mixed $domain
     *
     * @return bool
     */
    public static function haveAccess($nav = null, $roleId = null, $appId = null, $domain = null) {
        $canAccess = static::protectedhaveAccess($nav, $roleId, $appId, $domain);
        $accessCallback = CApp_Navigation::getAccessCallback($domain);
        if ($accessCallback != null && is_callable($accessCallback)) {
            $canAccess = call_user_func($accessCallback, $nav, $roleId, $appId, $domain, $canAccess);
        }

        return $canAccess;
    }

    /**
     * @param null|mixed $nav
     * @param null|mixed $roleId
     * @param null|mixed $appId
     * @param null|mixed $domain
     *
     * @return bool
     */
    protected static function protectedhaveAccess($nav = null, $roleId = null, $appId = null, $domain = null) {
        $app = CApp::instance();
        if ($roleId == null) {
            $role = $app->role();
            if ($role != null) {
                $roleId = $role->role_id;
            }
        }
        if ($appId == null) {
            $appId = CF::appId();
        }
        if ($nav == null) {
            $nav = self::nav();
        }

        if ($nav === false) {
            return false;
        }
        if (CApp::isAdministrator()) {
            return true;
        }
        $db = CDatabase::instance(null, null, $domain);
        if ($roleId == 'PUBLIC') {
            $roleId = null;
        }

        $role = c::app()->getRole($roleId);

        if ($role != null && $role->parent_id == null) {
            //is is superadmin
            return true;
        }

        if (!isset(self::$role_navs[$appId]) || !isset(self::$role_navs[$appId][$roleId])) {
            if (!isset(self::$role_navs[$appId])) {
                self::$role_navs[$appId] = [];
            }
            if (!isset(self::$role_navs[$appId][$roleId])) {
                $roleNavModel = CApp_Model_RoleNav::whereNull('role_id');
                if ($roleId != null) {
                    $roleNavModel = CApp_Model_RoleNav::where('role_id', '=', $roleId);
                }

                self::$role_navs[$appId][$roleId] = $roleNavModel->where('app_id', '=', $appId)->get()->pluck('nav')->toArray();
            }
        }

        return in_array($nav['name'], self::$role_navs[$appId][$roleId]);
    }

    /**
     * @param mixed      $action
     * @param null|mixed $nav
     * @param null|mixed $roleId
     * @param null|mixed $appId
     * @param null|mixed $domain
     *
     * @return bool
     */
    public static function havePermission($action, $nav = null, $roleId = null, $appId = null, $domain = null) {
        $app = c::app();
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

        if ($role == null) {
            $role = CApp_Auth_Role::getModel($roleId);
        }
        /** @var CApp_Model_Roles $role */
        if ($role == null) {
            return false;
        }
        if ($role != null && $role->parent_id == null) {
            return true;
        }

        return $role->rolePermission()->where('name', '=', $action)->where('app_id', '=', $appId)->count() > 0;
    }

    public static function appUserRightsArray($appId, $roleId, $appRoleId = '', $domain = null) {
        $navs = CApp_Navigation_Data::get($domain);

        return self::asUserRightsArray($appId, $roleId, $navs, $appRoleId, $domain);
    }

    public static function asUserRightsArray($appId, $roleId, $navs = null, $appRoleId = '', $domain = '', $level = 0) {
        if ($navs == null) {
            $navs = CNavigation::instance()->navs();
        }

        $result = [];

        foreach ($navs as $d) {
            if (!self::accessAvailable($d, $appId, $domain, $appRoleId)) {
                continue;
            }

            $res = $d;
            if (!is_array($res)) {
                throw new Exception('Error on nav structure on navs: ' . json_encode($navs));
            }

            $res['level'] = $level;
            $res['role_id'] = $roleId;
            $res['app_id'] = $appId;
            $res['domain'] = $domain;
            $subnav = [];
            if (isset($d['subnav']) && is_array($d['subnav']) && count($d['subnav']) > 0) {
                $subnav = self::asUserRightsArray($appId, $roleId, $d['subnav'], $appRoleId, $domain, $level + 1);
            }

            if (count($subnav) == 0 && ((!isset($d['controller']) || strlen($d['controller']) == 0))) {
                if (!isset($d['uri']) || strlen($d['uri']) == 0) {
                    continue;
                }
            }
            $result[] = $res;
            $result = array_merge($result, $subnav);
        }

        return $result;
    }

    /**
     * @param mixed $nav
     *
     * @return int
     */
    public static function childCount($nav) {
        if (isset($nav['subnav'])) {
            if (is_array($nav['subnav'])) {
                return count($nav['subnav']);
            }
        }

        return 0;
    }

    /**
     * @param mixed $nav
     *
     * @return bool
     */
    public static function haveChild($nav) {
        return self::childCount($nav) > 0;
    }

    /**
     * @param mixed $nav
     *
     * @return bool
     */
    public static function isLeaf($nav) {
        return isset($nav['subnav']) && is_array($nav['subnav']);
    }

    /**
     * @param mixed $nav
     *
     * @return bool
     */
    public static function url($nav) {
        $controller = '';
        $method = '';
        $path = '';
        $link = '';

        if (isset($nav['path'])) {
            $path = $nav['path'];
        }
        if (isset($nav['controller'])) {
            $controller = $nav['controller'];
        }
        if (isset($nav['method'])) {
            $method = $nav['method'];
        }
        if (isset($nav['link'])) {
            $link = $nav['link'];
        }

        if (strlen($link) > 0) {
            $url = $link;
        } else {
            if (strlen($path) > 0) {
                $path .= '/';
            }
            if (strlen($controller) == 0) {
                return '';
            }
            if (strlen($method) == 0) {
                return '';
            }
            $url = curl::base() . $path . $controller . '/' . $method;
        }

        return $url;
    }

    /**
     * @param null|mixed $nav
     * @param mixed      $appId
     * @param mixed      $domain
     * @param mixed      $appRoleId
     *
     * @return bool
     */
    public static function accessAvailable($nav = null, $appId = '', $domain = '', $appRoleId = '') {
        if ($nav == null) {
            $nav = self::nav();
        }
        if ($nav === false) {
            return false;
        }
        $navname = carr::get($nav, 'name');
        $app = CApp::instance();

        $appRole = null;
        if (strlen($appRoleId) == 0) {
            $appRole = $app->role();
            if ($appRole) {
                $appRoleId = c::get($appRole, 'role_id');
            }
        } else {
            $appRole = c::app()->getRole($appRoleId);
        }

        if (strlen($appRoleId) > 0) {
            if ($appRole != null && $appRole->parent_id == null) {
                return true;
            }
            if ($appRole != null && (!isset($nav['subnav']) || count($nav['subnav']) == 0)) {
                $parentRoleId = $appRole->parent_id;
                if ($parentRoleId != null) {
                    if (!self::haveAccess($nav, $appRoleId, $appId)) {
                        return false;
                    }
                }
            }
        }

        if (isset($nav['requirements'])) {
            $requirements = $nav['requirements'];
            foreach ($requirements as $k => $v) {
                $configValue = ccfg::get($k, $domain);
                if ($configValue != $v) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function permissionAvailable($action, $nav = null, $appId = '', $domain = '', $appRoleId = '') {
        if ($nav == null) {
            $nav = self::nav();
        }
        if ($nav === false) {
            return false;
        }

        if (!self::accessAvailable($nav, $appId, $domain, $appRoleId)) {
            return false;
        }

        $navname = $nav['name'];
        if (isset($nav['action'])) {
            $navactions = $nav['action'];
            foreach ($navactions as $act) {
                if ($act['name'] == $action && isset($act['requirements'])) {
                    $requirements = $act['requirements'];

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
        $isAdministrator = CApp::instance()->isAdministrator();
        if ($navs == null) {
            $navs = CNavigation::instance()->navs();
        }

        if ($navs == null) {
            return false;
        }
        $html = '';
        $child_count = 0;
        foreach ($navs as $d) {
            $child = 0;
            $pass = 0;
            $active_class = '';
            $controller = '';
            $method = '';
            $label = '';
            $icon = '';

            if (isset($d['controller'])) {
                $controller = $d['controller'];
            }
            if (isset($d['method'])) {
                $method = $d['method'];
            }
            if (isset($d['label'])) {
                $label = $d['label'];
            }
            if (isset($d['icon'])) {
                $icon = $d['icon'];
            }

            $child_html = '';

            if (isset($d['subnav'])) {
                $child_html .= self::render($d['subnav'], $level + 1, $child);
            }

            $url = self::url($d);

            if (!isset($url) || $url == null) {
                $url = '';
            }

            if (strlen($child_html) > 0 || strlen($url) > 0) {
                if (!self::accessAvailable($d, CF::appId(), CF::domain())) {
                    continue;
                }
                if (isset($d['controller']) && $d['controller'] != '') {
                    if (!$isAdministrator && ccfg::get('have_user_access')) {
                        if (!self::haveAccess($d)) {
                            continue;
                        }
                    }
                }

                $child_count++;

                $border = carr::get($d, 'border');

                $find_nav = self::nav($d);

                if ($find_nav !== false) {
                    $active_class = ' active';
                }

                $li_class = 'sidenav-item ';
                if ($child > 0) {
                    $li_class .= ' with-right-arrow';
                    if ($level == 0) {
                        $li_class .= ' dropdown';
                    } else {
                        $li_class .= ' dropdown-submenu ';
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
                $icon_html = '';
                if (strlen($iconClass) > 0) {
                    $icon_html = '<i class="' . $iconClass . '"></i>';
                }
                if ($url == '') {
                    $caret = '';
                    if ($level == 0) {
                        $caret = '<b class="caret">';
                    }

                    $elem = '<a class="' . $active_class . ' dropdown-toggle sidenav-link sidenav-toggle" href="javascript:;" data-toggle="dropdown">' . $icon_html . '<span>' . c::__($label) . '</span>' . $caret . '</b>';
                    if ($child > 0) {
                        //$elem .= '<span class="label">'.$child.'</span>';
                    }
                    $elem .= "</a>\r\n";
                } else {
                    $target = '';
                    $notif = '';
                    if (isset($d['target']) && strlen($d['target']) > 0) {
                        $target = ' target="' . $d['target'] . '"';
                    }
                    if (isset($d['notif_count'])) {
                        $callable = $d['notif_count'];

                        if (is_callable($callable)) {
                            $notif = call_user_func($callable);
                        }
                    }

                    $strNotif = '';
                    if ($notif != null && $notif > 0) {
                        $strNotif = ' <span class="label label-info nav-notif nav-notif-count">' . $notif . '</span>';
                    }
                    $elem = '<a class="' . $active_class . ' sidenav-link" href="' . $url . '"' . $target . '>' . $icon_html . '<span>' . c::__($label) . '</span>' . $strNotif . "</a>\r\n";
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
            $html = '';
        }
        $child = $child_count;

        return $html;
    }
}
