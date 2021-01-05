<?php

class CApp_Auth_Action_RedirectIfTwoFactorAuthenticatable {
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
        $user = $this->validateCredentials($request);

        if (c::optional($user)->two_factor_secret
            && in_array(TwoFactorAuthenticatable::class, c::classUsesRecursive($user))
        ) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param \CHTTP_Request $request
     *
     * @return mixed
     */
    protected function validateCredentials($request) {
        if (CApp_Auth::$authenticateUsingCallback) {
            return c::tap(call_user_func(CApp_Auth::$authenticateUsingCallback, $request), function ($user) use ($request) {
                if (!$user) {
                    $this->fireFailedEvent($request);

                    $this->throwFailedAuthenticationException($request);
                }
            });
        }

        $user = $this->guard->getProvider()->retrieveByCredentials(
            $request->only(CApp_Auth::username(), 'password')
        );

        if (!$user) {
            $this->fireFailedEvent($request, $user);

            $this->throwFailedAuthenticationException($request);
        }

        return $user;
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
     * @param CHTTP_Request                       $request
     * @param CAuth_AuthenticatableInterface|null $user
     *
     * @return void
     */
    protected function fireFailedEvent($request, $user = null) {
        c::event(new CAuth_Event_Failed(CF::config('app.auth.guard'), $user, [
            CApp_Auth::username() => $request->{CApp_Auth::username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     *
     * @param CHTTP_Request $request
     * @param mixed         $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function twoFactorChallengeResponse($request, $user) {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
        ]);

        return $request->wantsJson()
            ? c::response()->json(['two_factor' => true])
            : c::redirect()->setTargetUrl('login/twofactor');
    }
}
