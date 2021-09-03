<?php

class CApp_Auth_Action_EnsureLoginIsNotThrottled {
    /**
     * The login rate limiter instance.
     *
     * @var CApp_Auth_LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new class instance.
     *
     * @param CApp_Auth_LoginRateLimiter $limiter
     *
     * @return void
     */
    public function __construct(CApp_Auth_LoginRateLimiter $limiter) {
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param CHTTP_Request $request
     * @param callable      $next
     *
     * @return mixed
     */
    public function handle($request, $next) {
        if (!$this->limiter->tooManyAttempts($request)) {
            return $next($request);
        }

        c::event(new CAuth_Event_Lockout($request));

        return c::app(CApp_Auth_Response_LockoutResponse::class);
    }
}
