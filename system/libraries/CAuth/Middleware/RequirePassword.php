<?php

class CAuth_Middleware_RequirePassword {
    /**
     * The response factory instance.
     *
     * @var \CHTTP_ResponseFactory
     */
    protected $responseFactory;

    /**
     * The URL generator instance.
     *
     * @var \CRouting_UrlGenerator
     */
    protected $urlGenerator;

    /**
     * The password timeout.
     *
     * @var int
     */
    protected $passwordTimeout;

    /**
     * Create a new middleware instance.
     *
     * @param null|int $passwordTimeout
     *
     * @return void
     */
    public function __construct($passwordTimeout = null) {
        $this->responseFactory = CHTTP::responseFactory();
        $this->urlGenerator = CRouting::urlGenerator();
        $this->passwordTimeout = $passwordTimeout ?: 10800;
    }

    /**
     * Specify the redirect route and timeout for the middleware.
     *
     * @param null|string $redirectToRoute
     * @param null|string $passwordTimeoutSeconds
     *
     * @return string
     *
     * @named-arguments-supported
     */
    public static function using($redirectToRoute = null, $passwordTimeoutSeconds = null) {
        return static::class . ':' . implode(',', func_get_args());
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request  $request
     * @param \Closure        $next
     * @param null|string     $redirectToRoute
     * @param null|string|int $passwordTimeoutSeconds
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null) {
        if ($this->shouldConfirmPassword($request, $passwordTimeoutSeconds)) {
            if ($request->expectsJson()) {
                return $this->responseFactory->json([
                    'message' => 'Password confirmation required.',
                ], 423);
            }

            return $this->responseFactory->redirectGuest(c::url($redirectToRoute ?: 'password/confirm'));
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param \CHTTP_Request $request
     * @param null|int       $passwordTimeoutSeconds
     *
     * @return bool
     */
    protected function shouldConfirmPassword($request, $passwordTimeoutSeconds = null) {
        $confirmedAt = time() - $request->session()->get('auth.password_confirmed_at', 0);

        return $confirmedAt > ($passwordTimeoutSeconds ?? $this->passwordTimeout);
    }
}
