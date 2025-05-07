<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Navigation_Helper as Helper;

class CNavigation_Renderer_MenuRenderer extends CNavigation_RendererAbstract {
    public function render($navs = null, $level = 0, &$child = 0) {
        $domain = CF::domain();

        if ($navs == null && $level == 0) {
            $navs = $this->navs;
        }
        if ($navs == null) {
            return false;
        }
        $html = '';
        $childCount = 0;
        if (!is_array($navs)) {
            return '';
        }
        foreach ($navs as $d) {
            $child = 0;
            $pass = 0;
            $activeClass = '';
            $controller = carr::get($d, 'controller');
            $method = carr::get($d, 'method');
            $label = carr::get($d, 'label');
            $translate = carr::get($d, 'translate', true);
            $icon = carr::get($d, 'icon');
            $class = carr::get($d, 'class');
            $badge = carr::get($d, 'badge');
            if ($badge != null) {
                $badge = c::value($badge);
            }
            $childHtml = '';

            if (isset($d['subnav']) && is_array($d['subnav'])) {
                $childHtml .= $this->render(carr::get($d, 'subnav', []), $level + 1, $child);
            }
            $url = carr::get($d, 'uri');
            if ($url == null) {
                $url = Helper::url($d);
            }

            if (!isset($url) || $url == null) {
                $url = '';
            }

            if (strlen($childHtml) > 0 || strlen($url) > 0) {
                if (!Helper::accessAvailable($d, CF::appId(), $domain)) {
                    continue;
                }
                if (isset($d['controller']) && $d['controller'] != '') {
                    if (CF::config('app.have_user_access')) {
                        if (!Helper::haveAccess($d)) {
                            continue;
                        }
                    }
                }

                $childCount++;

                $border = carr::get($d, 'border');

                $findNav = Helper::nav($d);

                $isActive = $findNav !== false;
                $activeCallback = CNavigation::manager()->getActiveCallback($domain);
                if ($activeCallback != null) {
                    $isActive = CFunction::factory($activeCallback)->addArg($d)->addArg($isActive)->execute();
                }
                if ($isActive) {
                    $activeClass = ' active';
                    if (strlen(trim($childHtml)) > 0) {
                        $activeClass .= ' open';
                    }
                }

                $liClass = 'sidebar-item cres-sidebar-item ';
                if ($level > 0) {
                    $liClass .= 'submenu-item ';
                }
                $hasSubmenu = $url == '' ? true : false;
                if ($hasSubmenu) {
                    $liClass .= 'has-sub ';
                }
                $additionStyle = '';
                if ($border == 'top') {
                    $additionStyle = ' style="border-top:1px solid #bbb"';
                }
                if ($border == 'bottom') {
                    $additionStyle = ' style="border-bottom:1px solid #bbb"';
                }

                $html .= '<li class="' . $liClass . $class . $activeClass . '" ' . $additionStyle . '>';

                $iconClass = carr::get($d, 'icon');
                if (strlen($iconClass) > 0 && strpos($iconClass, 'fa-') === false && strpos($iconClass, 'ion-') === false) {
                    $iconClass = c::theme('icon.prefix', 'icon-') . $iconClass;
                }
                $icon_html = '';
                if (strlen($iconClass) > 0) {
                    $icon_html = '<i class="sidenav-icon ' . $iconClass . '"></i>';
                }
                if ($url == '') {
                    $caret = '';
                    if ($level == 0) {
                        $caret = '<b class="caret">';
                    }
                    $strBadge = '';
                    if ($badge != null) {
                        $strBadge = ' <span class="badge badge-info capp-nav-badge cres-nav-badge">' . $badge . '</span>';
                    }
                    $aClass = $level > 0 ? 'submenu-link' : 'sidebar-link';
                    $elem = '<a class="' . $activeClass . ' ' . $aClass . '" href="javascript:;" >' . $icon_html . '<span>' . ($translate ? c::__($label) : $label) . '</span>' . $strBadge . $caret . '</b>';
                    if ($child > 0) {
                        //$elem .= '<span class="label">'.$child.'</span>';
                    }
                    $elem .= "</a>\r\n";
                } else {
                    $url = '/' . trim($url, '/');
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
                    $strBadge = '';
                    if ($badge != null) {
                        $strBadge = ' <span class="badge badge-info capp-nav-badge cres-nav-badge">' . $badge . '</span>';
                    }
                    $aClass = $level > 0 ? 'submenu-link' : 'sidebar-link';
                    $elem = '<a class="' . $activeClass . ' ' . $aClass . '" href="' . $url . '"' . $target . '>' . $icon_html . '<span>' . ($translate ? c::__($label) : $label) . '</span>' . $strNotif . $strBadge . "</a>\r\n";
                }
                $html .= $elem;
                $html .= $childHtml;
                $html .= '</li>';
                $after = carr::get($d, 'after');
                if ($after && is_array($after)) {
                    $separator = carr::get($after, 'separator');
                    if ($separator == 'line') {
                        $html .= '<li class="sidebar-line cres-sidenav-item cres-sidenav-item-separator"><hr/></li>';
                    }
                    if ($separator == 'title') {
                        $html .= '<li class="sidebar-title cres-sidenav-item cres-sidenav-item-separator"><hr/></li>';
                    }
                }
            }
        }

        if (strlen($html) > 0) {
            if ($level == 0) {
                $html = "  <ul class=\"menu \">\r\n" . $html . "  </ul>\r\n";
            } else {
                $submenuLevelClass = $level > 1 ? 'submenu-level-' . $level : '';
                $html = '  <ul class="submenu ' . $submenuLevelClass . "\">\r\n" . $html . "  </ul>\r\n";
            }
        }
        if ($childCount == 0) {
            $html = '';
        }
        $child = $childCount;

        return $html;
    }
}
