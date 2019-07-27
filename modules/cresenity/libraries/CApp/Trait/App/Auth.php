<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:53:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Auth {

    private $_role = null;
    private $_user = null;

    public function is_user_login() {
        return $this->user() != null;
    }

    public function user() {
        if ($this->_user == null) {
            $session = CSession::instance();
            $user = $session->get("user");
            if (!$user)
                $user = null;
            $this->_user = $user;
        }
        return $this->_user;
    }

    public function role() {
        if ($this->_role == null) {
            $user = $this->user();
            if ($user != null) {
                $this->_role = crole::get(cobj::get($user, 'role_id'));
            }
        }
        return $this->_role;
    }

}
