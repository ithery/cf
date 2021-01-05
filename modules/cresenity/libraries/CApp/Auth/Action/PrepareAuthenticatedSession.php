<?php

class AttemptToAuthenticate {
    /**
     * The guard implementation.
     *
     * @var CAuth_StatefulGuardInterface
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var CApp_Auth_LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param CAuth_StatefulGuardInterface $guard
     * @param CApp_Auth_LoginRateLimiter   $limiter
     *
     * @return void
     */
    public function __construct(CAuth_StatefulGuardInterface $guard, CApp_Auth_LoginRateLimiter $limiter) {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable                 $next
     *
     * @return mixed
     */
    public function handle($request, $next) {
        if (CApp_Auth::$authenticateUsingCallback) {
            return $this->handleUsingCustomCallback($request, $next);
        }

        if ($this->guard->attempt(
            $request->only(CApp_Auth::username(), 'password'),
            $request->filled('remember')
        )
        ) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request);
    }

    /**
     * Attempt to authenticate using a custom callback.
     *
     * @param CHTTP_Request $request
     * @param callable      $next
     *
     * @return mixed
     */
    protected function handleUsingCustomCallback($request, $next) {
        $user = call_user_func(CApp_Auth::$authenticateUsingCallback, $request);

        if (!$user) {
            $this->fireFailedEvent($request);

            return $this->throwFailedAuthenticationException($request);
        }

        $this->guard->login($user, $request->filled('remember'));

        return $next($request);
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param CHTTP_Request $request
     *
     * @return void
     *
     * @throws CValidation_Exception
     */
    protected function throwFailedAuthenticationException($request) {
        $this->limiter->increment($request);

        throw CValidation_Exception::withMessages([
            CApp_Auth::username() => [c::trans('auth.failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param CHTTP_Request $request
     *
     * @return void
     */
    protected function fireFailedEvent($request) {
        c::event(new CAuth_Event_Failed(CF::config('app.auth.guard'), null, [
            CApp_Auth::username() => $request->{CApp_Auth::username()},
            'password' => $request->password,
        ]));
    }
}
