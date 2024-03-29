<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_App {
    /**
     * @var null|stdClass
     *
     * @deprecated
     */
    private $_store = null;

    /**
     * @var null|stdClass
     *
     * @deprecated
     */
    private $_admin = null;

    /**
     * @var null|stdClass
     *
     * @deprecated
     */
    private $_member = null;

    /**
     * @deprecated since version 1.2, please use function addBreadcrumb
     *
     * @param string $caption
     * @param string $url
     *
     * @return CApp
     */
    public function add_breadcrumb($caption, $url) {
        return $this->addBreadcrumb($caption, $url);
    }

    /**
     * @deprecated since version 1.2, please use function appId
     *
     * @return int
     */
    public function app_id() {
        return $this->appId();
    }

    /**
     * @deprecated since version 1.2, please use function showTitle
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function show_title($bool) {
        return $this->showTitle($bool);
    }

    /**
     * @deprecated since version 1.2, please use function showBreadcrumb
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function show_breadcrumb($bool) {
        return $this->showBreadcrumb($bool);
    }

    /**
     * @deprecated since version 1.2, please use function isAdmin
     *
     * @return bool
     */
    public function is_admin() {
        return $this->isAdmin();
    }

    /**
     * @deprecated since version 1.2, please use function orgId
     *
     * @return bool
     */
    public function org_id() {
        return $this->orgId();
    }

    /**
     * @deprecated since version 1.2, please use function isAdmin
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function set_login_required($bool) {
        return $this->setLoginRequired($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $js
     *
     * @return $this
     */
    public function add_custom_js($js) {
        return $this->addCustomJs($js);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param int $roleId
     * @param int $orgId
     *
     * @return array
     */
    public function get_role_child_list($roleId = null, $orgId = null) {
        return $this->getRoleChildList($roleId, $orgId);
    }

    /**
     * @deprecated
     *
     * @param array $data
     *
     * @return $this
     */
    public function set_custom_data($data) {
        return $this->setCustomData($data);
    }

    /**
     * @deprecated
     *
     * @param string $module
     */
    public function register_client_module($module) {
        CManager::instance()->registerModule($module);
    }

    /**
     * @return bool
     */
    public function is_user_login() {
        return $this->isUserLogin();
    }

    /**
     * @deprecated
     */
    public function set_view_html() {
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function admin() {
        if ($this->_admin == null) {
            $session = CSession::instance();
            $admin = $session->get('admin');
            if (!$admin) {
                $admin = null;
            }
            $this->_admin = $admin;
        }

        return $this->_admin;
    }

    /**
     * @deprecated 1.6
     *
     * @return object
     */
    public function member() {
        if ($this->_member = null) {
            $session = c::session();
            $member = $session->get('member');
            if (!$member) {
                $member = null;
            }
            $this->_member = $member;
        }

        return $this->_member;
    }

    /**
     * @return bool
     *
     * @deprecated
     */
    public function is_admin_login() {
        return $this->admin() != null;
    }

    /**
     * @return bool
     *
     * @deprecated
     */
    public function is_member_login() {
        return $this->member() != null;
    }

    /**
     * Undocumented function.
     *
     * @param [type] $str
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public function set_additional_head($str) {
        $this->additional_head = $str;
    }
}
//@codingStandardsIgnoreEnd
