<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 6:51:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Listener {
    
    public function set_confirm($bool) {    
        return $this->setConfirm($bool);
    }
    
    public function set_no_double($bool) {
        return $this->setNoDouble($bool);
    }    
    
    public function set_confirm_message($message) {
        return $this->setConfirmMessage($message);
    }    
    
    public function set_owner($owner) {
        return $this->setOwner($owner);
    }
    
    public function set_handler_url_param($param) {
        return $this->setHandlerUrlParam($param);
    }
    
    public function add_handler($handler_name) {
        return $this->addHandler($handler_name);
    }
}
