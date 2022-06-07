<?php
use Symfony\Component\HttpFoundation\Cookie;

class CHTTP_Middleware_VerifyCsrfToken {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The encrypter implementation.
     *
     * @var \CCrypt_EncrypterInterface
     */
    protected $encrypter;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct() {
        $this->encrypter = CCrypt::encrypter();
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     *
     * @throws \CSession_Exception_TokenMismatchException
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($this->isReading($request)
            || $this->runningUnitTests()
            || $this->inExceptArray($request)
            || $this->tokensMatch($request)
        ) {
            return c::tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new CSession_Exception_TokenMismatchException('CSRF token mismatch.');
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param \CHTTP_Request $request
     *
     * @return bool
     */
    protected function isReading($request) {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests() {
        return CF::isCli() && CF::isTesting();
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \CHTTP_Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request) {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param \CHTTP_Request $request
     *
     * @return bool
     */
    protected function tokensMatch($request) {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token())
               && is_string($token)
               && hash_equals($request->session()->token(), $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param \CHTTP_Request $request
     *
     * @return string
     */
    protected function getTokenFromRequest($request) {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = CHTTP_Cookie_CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
            } catch (CCrypt_Exception_DecryptException $e) {
                $token = '';
            }
        }

        return $token;
    }

    /**
     * Determine if the cookie should be added to the response.
     *
     * @return bool
     */
    public function shouldAddXsrfTokenCookie() {
        return $this->addHttpCookie;
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param \CHTTP_Request                             $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response) {
        $config = CF::config('session');

        if ($response instanceof CInterface_Responsable) {
            $response = $response->toResponse($request);
        }

        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                $this->availableAt($config['expiration']),
                $config['path'],
                $config['domain'],
                $config['secure'],
                false,
                false,
                isset($config['same_site']) ? $config['same_site'] : null
            )
        );

        return $response;
    }

    /**
     * Determine if the cookie contents should be serialized.
     *
     * @return bool
     */
    public static function serialized() {
        return CHTTP_Cookie_Middleware_EncryptCookies::serialized('XSRF-TOKEN');
    }
}
