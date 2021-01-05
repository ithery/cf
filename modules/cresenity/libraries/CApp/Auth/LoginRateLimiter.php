<?php

class LoginRateLimiter {
    /**
     * The login rate limiter instance.
     *
     * @var CCache_RateLimiter
     */
    protected $limiter;

    /**
     * Create a new login rate limiter instance.
     *
     * @param CCache_RateLimiter $limiter
     *
     * @return void
     */
    public function __construct(CCache_RateLimiter $limiter) {
        $this->limiter = $limiter;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param CHTTP_Request $request
     *
     * @return mixed
     */
    public function attempts(CHTTP_Request $request) {
        return $this->limiter->attempts($this->throttleKey($request));
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param CHTTP_Request $request
     *
     * @return bool
     */
    public function tooManyAttempts(CHTTP_Request $request) {
        return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param CHTTP_Request $request
     *
     * @return void
     */
    public function increment(CHTTP_Request $request) {
        $this->limiter->hit($this->throttleKey($request), 60);
    }

    /**
     * Determine the number of seconds until logging in is available again.
     *
     * @param CHTTP_Request $request
     *
     * @return int
     */
    public function availableIn(CHTTP_Request $request) {
        return $this->limiter->availableIn($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param CHTTP_Request $request
     *
     * @return void
     */
    public function clear(CHTTP_Request $request) {
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param CHTTP_Request $request
     *
     * @return string
     */
    protected function throttleKey(CHTTP_Request $request) {
        return cstr::lower($request->input(CApp_Auth::username())) . '|' . $request->ip();
    }
}
