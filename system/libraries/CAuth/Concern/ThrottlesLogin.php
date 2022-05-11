<?php

trait CAuth_Concern_ThrottlesLogin {
    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \CHTTP_Request $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(CHTTP_Request $request) {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param \CHTTP_Request $request
     *
     * @return void
     */
    protected function incrementLoginAttempts(CHTTP_Request $request) {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param \CHTTP_Request $request
     *
     * @throws \CValidation_Exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendLockoutResponse(CHTTP_Request $request) {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw CValidation_Exception::withMessages([
            $this->username() => [c::__('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(CHTTP_Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param \CHTTP_Request $request
     *
     * @return void
     */
    protected function clearLoginAttempts(CHTTP_Request $request) {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param \CHTTP_Request $request
     *
     * @return void
     */
    protected function fireLockoutEvent(CHTTP_Request $request) {
        c::event(new CAuth_Event_Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \CHTTP_Request $request
     *
     * @return string
     */
    protected function throttleKey(CHTTP_Request $request) {
        return cstr::transliterate(cstr::lower($this->username()) . '|' . $request->ip());
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \CCache_RateLimiter
     */
    protected function limiter() {
        return CCache::rateLimiter();
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts() {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes() {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }
}
