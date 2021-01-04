<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 27, 2019, 10:53:10 PM
 */
trait CApp_Trait_App_Auth {
    private $role = null;
    private $user = null;
    private $loginRequired = true;

    public function isUserLogin() {
        return $this->user() != null;
    }

    public function setLoginRequired($bool) {
        $this->loginRequired = $bool;
        return $this;
    }

    public function isLoginRequired() {
        return $this->loginRequired;
    }

    public function user() {
        if ($this->user == null) {
            $session = CSession::instance();
            $user = $session->get('user');
            if (!$user) {
                $user = null;
            }
            $this->user = $user;
        }
        return $this->user;
    }

    public function role() {
        if ($this->role == null) {
            $user = $this->user();
            if ($user != null) {
                $this->role = crole::get(cobj::get($user, 'role_id'));
            }
        }
        return $this->role;
    }
}
