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

    private $roleResolver = null;

    /**
     * @var string
     */
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
                $this->guard = CF::config('auth.defaults.guard');
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
        if (!$this->authEnabled) {
            return false;
        }

        return $this->user() != null;
    }

    public function setLoginRequired($bool) {
        return $this->setAuthEnable($bool);
    }

    /**
     * Alias of isAuthEnabled.
     *
     * @deprecated use isAuthEnabled
     *
     * @return bool
     */
    public function isLoginRequired() {
        return $this->isAuthEnabled();
    }

    /**
     * @return null|CModel|object
     */
    public function user() {
        return $this->auth()->user();
    }

    public function setRoleResolver($resolver) {
        $this->roleResolver = $resolver;
        $this->role = null;

        return $this;
    }

    protected function defaultRoleResolver() {
        return function () {
            if (!CSession::sessionConfigured()) {
                return null;
            }
            $user = $this->user();
            if ($user) {
                $modelClass = $this->auth()->getRoleModelClass();
                $model = new $modelClass();
                /** @var CApp_Model_Roles $model */
                $keyName = $model->getKeyName();
                $roleId = $user->$keyName;

                return $this->getRole($roleId);
            }
        };
    }

    protected function resolveRole() {
        $resolver = $this->roleResolver ?: $this->defaultRoleResolver();

        return $resolver();
    }

    /**
     * Get Role Object.
     *
     * @return CApp_Model_Roles
     */
    public function role() {
        if ($this->role == null) {
            $this->role = $this->resolveRole();
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
        if ($this->user() != null) {
            if ($this->user()->role_id == $roleId && $this->role != null) {
                return $this->role;
            }
        }
        $modelClass = $this->auth()->getRoleModelClass();

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

        return $this->auth()->guard()->getProvider()->retrieveById($userId);
    }

    public function isAuthEnabled() {
        return $this->authEnabled;
    }

    public function setAuthEnable($bool = true) {
        $this->authEnabled = $bool;

        return $this;
    }

    public function enableAuth() {
        return $this->setAuthEnable(true);
    }

    public function disableAuth() {
        return $this->setAuthEnable(false);
    }
}
