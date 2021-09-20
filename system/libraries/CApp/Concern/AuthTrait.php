<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 27, 2019, 10:53:10 PM
 */
trait CApp_Concern_AuthTrait {
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

            if ($user) {
                $config = CF::config('app');
                $modelClass = c::get($config, 'model.user');
                $model = new $modelClass;
                $keyName = $model->getKeyName();
                $keyValue = c::get($user, $keyName);
                $user = $model->find($keyValue);
            }

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

            if ($user) {
                $modelClass = CF::config('app.model.role', CApp_Model_Roles::class);
                $model = new $modelClass;
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
     * @return CModel|CApp_Model_Roles|null
     */
    public function getRole($roleId) {
        if ($roleId == null) {
            return null;
        }
        $modelClass = CF::config('app.model.role', CApp_Model_Roles::class);
        return $modelClass::find($roleId);
    }
}
