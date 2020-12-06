<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 1, 2018, 11:57:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Navigation_Helper as Helper;

class CApp_Navigation_Engine_SideNav extends CApp_Navigation_Engine {

    public function render($navs = null, $level = 0, &$child = 0) {
        $domain = CF::domain();
        $is_admin = CApp::isAdministrator();
        if ($navs == null && $level == 0) {
            $navs = $this->navs;
        }
        if ($navs == null) {
            return false;
        }
        $html = "";
        $childCount = 0;
        if(!is_array($navs)) {
            return '';
        }
        foreach ($navs as $d) {

            $child = 0;
            $pass = 0;
            $activeClass = "";
            $controller = carr::get($d, 'controller');
            $method = carr::get($d, 'method');
            $label = carr::get($d, 'label');
            $icon = carr::get($d, 'icon');
            $class = carr::get($d, 'class');

            $childHtml = "";

            if (isset($d["subnav"]) && is_array($d["subnav"])) {
                $childHtml .= self::render(carr::get($d, 'subnav', array()), $level + 1, $child);
            }
            $url = carr::get($d, 'uri');
            if ($url == null) {
                $url = Helper::url($d);
            }

            if (!isset($url) || $url == null) {
                $url = "";
            }



            if (strlen($childHtml) > 0 || strlen($url) > 0) {

                if (!Helper::accessAvailable($d, CF::appId(), $domain)) {
                    continue;
                }
                if (isset($d["controller"]) && $d["controller"] != "") {
                    if (!$is_admin && ccfg::get("have_user_access")) {

                        if (!Helper::haveAccess($d)) {
                            continue;
                        }
                    }
                }

                $childCount++;

                $border = carr::get($d, 'border');

                $findNav = Helper::nav($d);

                $isActive = $findNav !== false;
                $activeCallback = CApp_Navigation::getActiveCallback($domain);
                if ($activeCallback != null) {
                    $isActive = CFunction::factory($activeCallback)->addArg($d)->addArg($isActive)->execute();
                }
                if ($isActive) {
                    $activeClass = " active open";
                }

                $li_class = "sidenav-item ";

                $addition_style = '';
                if ($border == 'top') {
                    $addition_style = ' style="border-top:1px solid #bbb"';
                }
                if ($border == 'bottom') {
                    $addition_style = ' style="border-bottom:1px solid #bbb"';
                }

                $html .= '<li class="' . $li_class . $class . $activeClass . '" ' . $addition_style . '>';

                $iconClass = carr::get($d, 'icon');
                if (strlen($iconClass) > 0 && strpos($iconClass, 'fa-') === false && strpos($iconClass, 'ion-') === false) {
                    $iconClass = 'icon-' . $iconClass;
                }
                $icon_html = "";
                if (strlen($iconClass) > 0) {
                    $icon_html = '<i class="sidenav-icon ' . $iconClass . '"></i>';
                }
                if ($url == "") {
                    $caret = "";
                    if ($level == 0) {
                        $caret = '<b class="caret">';
                    }

                    $elem = '<a class="' . $activeClass . ' sidenav-link sidenav-toggle" href="javascript:;" >' . $icon_html . '<span>' . clang::__($label) . '</span>' . $caret . '</b>';
                    if ($child > 0) {
                        //$elem .= '<span class="label">'.$child.'</span>';
                    }
                    $elem .= "</a>\r\n";
                } else {
                    $url = '/' . trim($url, '/');
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
                    $elem = '<a class="' . $activeClass . ' sidenav-link" href="' . $url . '"' . $target . '>' . $icon_html . '<span>' . clang::__($label) . "</span>" . $strNotif . "</a>\r\n";
                }
                $html .= $elem;
                $html .= $childHtml;
                $html .= '</li>';
            }
        }
        if (strlen($html) > 0) {
            if ($level == 0) {

                $html = "  <ul class=\"sidenav-inner \">\r\n" . $html . "  </ul>\r\n";
            } else {
                $html = "  <ul class=\"sidenav-menu \">\r\n" . $html . "  </ul>\r\n";
            }
        }
        if ($childCount == 0) {
            $html = "";
        }
        $child = $childCount;

        return $html;
    }

}
