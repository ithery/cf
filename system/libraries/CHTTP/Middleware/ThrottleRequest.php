<?php

use Symfony\Component\HttpFoundation\Response;

class CHTTP_Middleware_ThrottleRequest {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The rate limiter instance.
     *
     * @var \CCache_RateLimiter
     */
    protected $limiter;

    /**
     * Create a new request throttler.
     *
     * @return void
     */
    public function __construct() {
        $this->limiter = CCache::rateLimiter();
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHttp_Request $request
     * @param \Closure       $next
     * @param int|string     $maxAttempts
     * @param float|int      $decayMinutes
     * @param string         $prefix
     *
     * @throws \CHTTP_Exception_ThrottleRequestException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '') {
        if (is_string($maxAttempts)
            && func_num_args() === 3
            && !is_null($limiter = $this->limiter->limiter($maxAttempts))
        ) {
            return $this->handleRequestUsingNamedLimiter($request, $next, $maxAttempts, $limiter);
        }

        return $this->handleRequest(
            $request,
            $next,
            [
                (object) [
                    'key' => $prefix . $this->resolveRequestSignature($request),
                    'maxAttempts' => $this->resolveMaxAttempts($request, $maxAttempts),
                    'decayMinutes' => $decayMinutes,
                    'responseCallback' => null,
                ],
            ]
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     * @param string         $limiterName
     * @param \Closure       $limiter
     *
     * @throws \CHTTP_Exception_ThrottleRequestException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequestUsingNamedLimiter($request, Closure $next, $limiterName, Closure $limiter) {
        $limiterResponse = call_user_func($limiter, $request);

        if ($limiterResponse instanceof Response) {
            return $limiterResponse;
        } elseif ($limiterResponse instanceof CCache_RateLimiting_Unlimited) {
            return $next($request);
        }

        return $this->handleRequest(
            $request,
            $next,
            c::collect(carr::wrap($limiterResponse))->map(function ($limit) use ($limiterName) {
                return (object) [
                    'key' => md5($limiterName . $limit->key),
                    'maxAttempts' => $limit->maxAttempts,
                    'decayMinutes' => $limit->decayMinutes,
                    'responseCallback' => $limit->responseCallback,
                ];
            })->all()
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     * @param array          $limits
     *
     * @throws \CHTTP_Exception_ThrottleRequestException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest($request, Closure $next, array $limits) {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                throw $this->buildException($request, $limit->key, $limit->maxAttempts, $limit->responseCallback);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     *
     * @param \CHTTP_Request $request
     * @param int|string     $maxAttempts
     *
     * @return int
     */
    protected function resolveMaxAttempts($request, $maxAttempts) {
        if (cstr::contains($maxAttempts, '|')) {
            $maxAttempts = explode('|', $maxAttempts, 2)[$request->user() ? 1 : 0];
        }

        if (!is_numeric($maxAttempts) && $request->user()) {
            $maxAttempts = $request->user()->{$maxAttempts};
        }

        return (int) $maxAttempts;
    }

    /**
     * Resolve request signature.
     *
     * @param \CHTTP_Request $request
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function resolveRequestSignature($request) {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        } elseif ($route = $request->route()) {
            return sha1($route->getDomain() . '|' . $request->ip());
        } else {
            return sha1(CF::domain() . '|' . $request->ip());
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param \CHTTP_Request $request
     * @param string         $key
     * @param int            $maxAttempts
     * @param null|callable  $responseCallback
     *
     * @return \CHTTP_Exception_ThrottleRequestException
     */
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null) {
        $retryAfter = $this->getTimeUntilNextRetry($key);
        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return is_callable($responseCallback)
                    ? new CHTTP_Exception_ResponseException($responseCallback($request, $headers))
                    : new CHTTP_Exception_ThrottleRequestException('Too Many Attempts.', null, $headers);
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param string $key
     *
     * @return int
     */
    protected function getTimeUntilNextRetry($key) {
        return $this->limiter->availableIn($key);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int                                        $maxAttempts
     * @param int                                        $remainingAttempts
     * @param null|int                                   $retryAfter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null) {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter, $response)
        );

        return $response;
    }

    /**
     * Get the limit headers information.
     *
     * @param int                                             $maxAttempts
     * @param int                                             $remainingAttempts
     * @param null|int                                        $retryAfter
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     *
     * @return array
     */
    protected function getHeaders(
        $maxAttempts,
        $remainingAttempts,
        $retryAfter = null,
        Response $response = null
    ) {
        if ($response
            && !is_null($response->headers->get('X-RateLimit-Remaining'))
            && (int) $response->headers->get('X-RateLimit-Remaining') <= (int) $remainingAttempts
        ) {
            return [];
        }

        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param string   $key
     * @param int      $maxAttempts
     * @param null|int $retryAfter
     *
     * @return int
     */
    protected function calculateRemainingAttempts($key, $maxAttempts, $retryAfter = null) {
        return is_null($retryAfter) ? $this->limiter->retriesLeft($key, $maxAttempts) : 0;
    }
}
