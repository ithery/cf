<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 29, 2018, 10:57:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Curl {

    /**
     * 
     * @deprecated since version 1.2, please use function addBreadcrumb
     * @return CCurl
     */
    public function set_ssl() {
        return $this->setSSL();
    }

}
