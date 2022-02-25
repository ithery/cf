<?php

class CHTTP_Cookie_Middleware_AddQueuedCookiesToResponse {
    /**
     * The cookie jar instance.
     *
     * @var \CHTTP_Cookie
     */
    protected $cookies;

    /**
     * Create a new CookieQueue instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cookies = CHTTP::cookie();
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $response = $next($request);

        /** @var CHTTP_Response $response */
        foreach ($this->cookies->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
