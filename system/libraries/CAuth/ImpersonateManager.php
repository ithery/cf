<?php

class CAuth_ImpersonateManager {
    const REMEMBER_PREFIX = 'remember_web';

    private static $instance;

    /**
     * @return CAuth_ImpersonateManager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param int        $id
     * @param null|mixed $guardName
     *
     * @throws CAuth_Exception_MissingUserProviderException
     * @throws CAuth_Exception_InvalidUserProviderException
     * @throws CModel_Exception_ModelNotFoundException
     *
     * @return \CAuth_AuthenticatableInterface|CAuth_Contract_ImpersonateableInterface
     */
    public function findUserById($id, $guardName = null) {
        if (empty($guardName)) {
            $guardName = c::app()->auth()->guardName();
        }

        $providerName = CF::config("auth.guards.${guardName}.provider");

        if (empty($providerName)) {
            throw new CAuth_Exception_MissingUserProviderException($guardName);
        }

        try {
            /** @var CAuth_UserProviderInterface $userProvider */
            $userProvider = c::auth()->createUserProvider($providerName);
        } catch (\InvalidArgumentException $e) {
            throw new CAuth_Exception_InvalidUserProviderException($guardName);
        }

        if (!($modelInstance = $userProvider->retrieveById($id))) {
            $model = CF::config("auth.providers.${providerName}.model");

            throw (new CModel_Exception_ModelNotFoundException())->setModel(
                $model,
                $id
            );
        }

        return $modelInstance;
    }

    public function isImpersonating() {
        return c::session()->has($this->getSessionKey());
    }

    /**
     * @return null|int
     */
    public function getImpersonatorId() {
        return c::session($this->getSessionKey(), null);
    }

    /**
     * @return \CAuth_AuthenticatableInterface
     */
    public function getImpersonator() {
        $id = c::session($this->getSessionKey(), null);

        return is_null($id) ? null : $this->findUserById($id, $this->getImpersonatorGuardName());
    }

    /**
     * @return null|string
     */
    public function getImpersonatorGuardName() {
        return c::session($this->getSessionGuard(), null);
    }

    /**
     * @return null|string
     */
    public function getImpersonatorGuardUsingName() {
        return c::session($this->getSessionGuardUsing(), null);
    }

    /**
     * @param \CAuth_AuthenticatableInterface $from
     * @param \CAuth_AuthenticatableInterface $to
     * @param null|string                     $guardName
     *
     * @return bool
     */
    public function start($from, $to, $guardName = null) {
        $this->saveAuthCookieInSession();

        try {
            $currentGuard = $this->getCurrentAuthGuardName();
            c::session()->put($this->getSessionKey(), $from->getAuthIdentifier());
            c::session()->put($this->getSessionGuard(), $currentGuard);
            c::session()->put($this->getSessionGuardUsing(), $guardName);

            c::auth()->guard($currentGuard)->quietLogout();
            c::auth()->guard($guardName)->quietLogin($to);
        } catch (\Exception $e) {
            unset($e);

            return false;
        }

        c::event()->dispatch(new CAuth_Event_StartImpersonate($from, $to));

        return true;
    }

    public function stop() {
        try {
            $impersonated = c::auth()->guard($this->getImpersonatorGuardUsingName())->user();
            $impersonator = $this->findUserById($this->getImpersonatorId(), $this->getImpersonatorGuardName());
            c::auth()->guard($this->getCurrentAuthGuardName())->quietLogout();
            c::auth()->guard($this->getImpersonatorGuardName())->quietLogin($impersonator);

            $this->extractAuthCookieFromSession();

            $this->clear();
        } catch (\Exception $e) {
            throw $e;
            unset($e);

            return false;
        }

        if ($impersonated && $impersonator) {
            c::event()->dispatch(new CAuth_Event_StopImpersonate($impersonator, $impersonated));
        }

        return true;
    }

    public function clear() {
        c::session()->forget($this->getSessionKey());
        c::session()->forget($this->getSessionGuard());
        c::session()->forget($this->getSessionGuardUsing());
    }

    public function getSessionKey() {
        return CF::config('auth.impersonate.session_key');
    }

    public function getSessionGuard() {
        return CF::config('auth.impersonate.session_guard');
    }

    public function getSessionGuardUsing() {
        return CF::config('auth.impersonate.session_guard_using');
    }

    public function getDefaultSessionGuard(): string {
        return CF::config('auth.impersonate.default_impersonator_guard');
    }

    public function getTakeRedirectTo(): string {
        $uri = CF::config('auth.impersonate.take_redirect_to');

        return $uri;
    }

    public function getLeaveRedirectTo() {
        $uri = CF::config('auth.impersonate.leave_redirect_to');

        return $uri;
    }

    /**
     * @return null|array
     */
    public function getCurrentAuthGuardName() {
        return c::app()->auth()->guardName();
        // $guards = array_keys(CF::config('auth.guards'));
        // foreach ($guards as $guard) {
        //     if (c::auth()->guard($guard)->check()) {
        //         return $guard;
        //     }
        // }

        // return null;
    }

    protected function saveAuthCookieInSession() {
        $cookie = $this->findByKeyInArray(c::request()->cookies->all(), static::REMEMBER_PREFIX);
        $key = $cookie->keys()->first();
        $val = $cookie->values()->first();

        if (!$key || !$val) {
            return;
        }

        c::session()->put(static::REMEMBER_PREFIX, [
            $key,
            $val,
        ]);
    }

    protected function extractAuthCookieFromSession() {
        if (!$session = $this->findByKeyInArray(c::session()->all(), static::REMEMBER_PREFIX)->first()) {
            return;
        }

        c::cookie()->queue($session[0], $session[1]);
        c::session()->forget($session);
    }

    /**
     * @param array  $values
     * @param string $search
     *
     * @return \CCollection
     */
    protected function findByKeyInArray(array $values, $search) {
        return c::collect($values ?: c::session()->all())
            ->filter(function ($val, $key) use ($search) {
                return strpos($key, $search) !== false;
            });
    }
}
