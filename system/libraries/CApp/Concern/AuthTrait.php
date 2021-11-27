<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CApp
 * @since Jul 27, 2019, 10:53:10 PM
 */
trait CApp_Concern_AuthTrait {
    protected $authEnabled = true;

    private $role = null;

    private $guard = null;

    /**
     * @param null|string $guard
     *
     * @return CApp_Auth
     */
    public function auth($guard = null) {
        if ($guard === null) {
            $guard = $this->guard;
            if ($this->guard === null) {
                $this->guard = CF::config('app.auth.guard');
            }
            $guard = $this->guard;
        }

        return CApp_Auth::instance($guard);
    }

    /**
     * Get Auth Instance.
     *
     * @param null|mixed $guard
     *
     * @return CApp_Auth;
     */
    public function setAuth($guard = null) {
        return $this->guard = $guard;
    }

    public function isUserLogin() {
        return $this->user() != null;
    }

    public function setLoginRequired($bool) {
        return $this->setAuthEnable($bool);
    }

    public function isLoginRequired() {
        return $this->isAuthEnabled();
    }

    public function user() {
        return $this->auth()->user();
    }

    public function role() {
        if ($this->role == null) {
            $user = $this->user();

            if ($user) {
                $modelClass = CF::config('app.model.role', CApp_Model_Roles::class);
                $model = new $modelClass();
                /** @var CApp_Model_Roles $model */
                $keyName = $model->getKeyName();
                $roleId = $user->$keyName;
                $this->role = $this->getRole($roleId);
            }
        }

        return $this->role;
    }

    /**
     * @param int $roleId
     *
     * @return null|CModel|CApp_Model_Roles
     */
    public function getRole($roleId) {
        if ($roleId == null) {
            return null;
        }
        $modelClass = CF::config('app.model.role', CApp_Model_Roles::class);

        return $modelClass::find($roleId);
    }

    /**
     * @param int $userId
     *
     * @return null|CModel|CApp_Model_Users
     */
    public function getUser($userId) {
        if ($userId == null) {
            return null;
        }
        $modelClass = CF::config('app.model.user', CApp_Model_Users::class);

        return $modelClass::find($userId);
    }

    public function isAuthEnabled() {
        return $this->authEnabled;
    }

    public function setAuthEnable($bool = true) {
        $this->authEnabled = $bool;

        return $this;
    }

    public function enableAuth() {
        return $this->setAuth(true);
    }

    public function disableAuth() {
        return $this->setAuth(false);
    }
}
