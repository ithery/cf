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
     * @deprecated since version 1.2, please use function appId
     * @return $this
     */
    public function show_title($bool) {
        return $this->showTitle($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function isAdmin
     * @return bool
     */
    public function is_admin() {
        return $this->isAdmin();
    }

}
