<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Navigation_Helper as Helper;

abstract class CNavigation_RendererAbstract implements CApp_Navigation_EngineInterface {
    protected $roleNavs = [];

    protected $roleId = null;

    protected $appId = null;

    protected $navs = null;

    public function __construct($options = []) {
        $app = CApp::instance();

        $roleId = carr::get($options, 'role_id');
        $appId = carr::get($options, 'app_id');
        $navs = carr::get($options, 'navs');

        if ($roleId == null) {
            $role = $app->role();
            if ($role != null) {
                $roleId = $role->role_id;
            }
        }
        if ($appId == null) {
            $appId = CF::appId();
        }
        $this->roleId = $roleId;
        $this->appId = $appId;

        /* get nav */
        if ($navs == null) {
            $navs = CApp_Navigation_Data::get();
        }

        $this->navs = $navs;

        $this->roleNavs = [];

        if (!CApp::isAdministrator()) {
            if (CApp::instance()->isAuthEnabled()) {
                $db = c::db();
                $q = 'select nav from role_nav where role_id=' . $db->escape($roleId) . ' and app_id=' . $db->escape($appId);
                if ($roleId == null) {
                    $q = 'select nav from role_nav where role_id is null and app_id=' . $db->escape($appId);
                }
                $this->roleNavs = $db->getArray($q);
            }
        }
    }
}
