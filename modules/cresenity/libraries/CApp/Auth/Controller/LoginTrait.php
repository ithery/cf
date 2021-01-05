<?php

trait CApp_Auth_Controller_LoginTrait {
    /**
     * Attempt to authenticate a new session.
     *
     * @param CHTTP_Request $request
     *
     * @return mixed
     */
    public function store(CHTTP_Request $request) {
        $this->validate($request, [
            CApp_Auth::username() => 'required|string',
            'password' => 'required|string',
        ]);

        return $this->loginPipeline($request)->then(function ($request) {
            return c::app(CApp_Auth_Response_LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param CHTTP_Request $request
     *
     * @return CBase_Pipeline
     */
    protected function loginPipeline(CHTTP_Request $request) {
        if (CApp_Auth::$authenticateThroughCallback) {
            return (new CBase_Pipeline(c::app()))->send($request)->through(array_filter(
                call_user_func(CApp_Auth::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(CF::config('app.auth.pipelines.login'))) {
            return (new CBase_Pipeline(c::app()))->send($request)->through(array_filter(
                CF::config('fortify.pipelines.login')
            ));
        }

        return (new CBase_Pipeline(c::app()))->send($request)->through(array_filter([
            CF::config('app.auth.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            CApp_Auth_Features::enabled(CApp_Auth_Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
}
