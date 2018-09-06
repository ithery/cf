<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:40:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_App {

    /**
     * 
     * @deprecated since version 1.2, please use function addBreadcrumb
     * @param string $caption
     * @param string $url
     * @return CApp
     */
    public function add_breadcrumb($caption, $url) {
        return $this->addBreadcrumb($caption, $url);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function appId
     * @return int
     */
    public function app_id() {
        return $this->appId();
    }

    /**
     * 
     * @deprecated since version 1.2, please use function showTitle
     * @param bool $bool
     * @return $this
     */
    public function show_title($bool) {
        return $this->showTitle($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function showBreadcrumb
     * @param bool $bool
     * @return $this
     */
    public function show_breadcrumb($bool) {
        return $this->showBreadcrumb($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function isAdmin
     * @return bool
     */
    public function is_admin() {
        return $this->isAdmin();
    }

    /**
     * 
     * @deprecated since version 1.2, please use function orgId
     * @return bool
     */
    public function org_id() {
        return $this->orgId();
    }

    /**
     * 
     * @deprecated since version 1.2, please use function isAdmin
     * @param bool $bool
     * @return $this
     */
    public function set_login_required($bool) {
        return $this->setLoginRequired($bool);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @param string $js
     * @return $this
     */
    public function add_custom_js($js) {
        return $this->addCustomJs($js);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @param int $roleId
     * @param int $orgId
     * @return array
     */
    public function get_role_child_list($roleId = null, $orgId = null) {
        return $this->getRoleChildList($roleId, $orgId);
    }

    /**
     * 
     * @deprecated
     * @param array $data
     * @return $this
     */
    public function set_custom_data($data) {
        return $this->setCustomData($data);
    }

}
