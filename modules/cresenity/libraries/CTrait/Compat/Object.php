<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 3:42:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Object {

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function regenerate_id() {
        return $this->regenerateId();
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return $this
     */
    public function add_friend($classname) {
        return $this->addFriend($classname);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return $this
     */
    public function set_domain($domain) {
        return $this->setDomain($domain);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function class_name() {
        return $this->className();
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return $this
     */
    static public function is_instanceof($value) {
        return $this->isInstanceof($value);
    }

}
