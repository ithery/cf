<?php
abstract class CMiddleware_TrustHost {
    /**
     * Create a new middleware instance.
     */
    public function __construct() {
    }

    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    abstract public function hosts();

    /**
     * Handle the incoming request.
     *
     * @param \CHTTP_Request $request
     * @param callable       $next
     *
     * @return \CHTTP_Response
     */
    public function handle(CHTTP_Request $request, $next) {
        if ($this->shouldSpecifyTrustedHosts()) {
            CHTTP_Request::setTrustedHosts(array_filter($this->hosts()));
        }

        return $next($request);
    }

    /**
     * Determine if the application should specify trusted hosts.
     *
     * @return bool
     */
    protected function shouldSpecifyTrustedHosts() {
        return CF::config('app.env') !== 'local'
               && !CF::isTesting() && !CF::isDevSuite();
    }

    /**
     * Get a regular expression matching the application URL and all of its subdomains.
     *
     * @return null|string
     */
    protected function allSubdomainsOfApplicationUrl() {
        if ($host = parse_url(CF::config('app.url'), PHP_URL_HOST)) {
            return '^(.+\.)?' . preg_quote($host) . '$';
        }
    }
}
