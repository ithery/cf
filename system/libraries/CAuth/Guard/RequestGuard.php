<?php

class CAuth_Guard_RequestGuard implements CAuth_GuardInterface {
    use CAuth_Guard_Concern_GuardHelper, CTrait_Macroable;

    /**
     * The guard callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The request instance.
     *
     * @var CHTTP_Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
     *
     * @param callable                         $callback
     * @param CHTTP_Request                    $request
     * @param CAuth_UserProviderInterface|null $provider
     *
     * @return void
     */
    public function __construct(callable $callback, CHTTP_Request $request, CAuth_UserProviderInterface $provider = null) {
        $this->request = $request;
        $this->callback = $callback;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function user() {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func(
            $this->callback,
            $this->request,
            $this->getProvider()
        );
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []) {
        return !is_null((new static(
            $this->callback,
            $credentials['request'],
            $this->getProvider()
        ))->user());
    }

    /**
     * Set the current request instance.
     *
     * @param \CHTTP_Request $request
     *
     * @return $this
     */
    public function setRequest(CHTTP_Request $request) {
        $this->request = $request;

        return $this;
    }
}
