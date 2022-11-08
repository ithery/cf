<?php

class CHTTP_Middleware_ValidatePostSize {
    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     *
     * @throws \CHTTP_Exception_PostTooLargeException;
     *
     * @return mixed
     */
    public function handle($request, $next) {
        $max = $this->getPostMaxSize();

        if ($max > 0 && ((int) $request->server('CONTENT_LENGTH')) > $max) {
            throw new CHTTP_Exception_PostTooLargeException();
        }

        return $next($request);
    }

    /**
     * Determine the server 'post_max_size' as bytes.
     *
     * @return int
     */
    protected function getPostMaxSize() {
        if (is_numeric($postMaxSize = ini_get('post_max_size'))) {
            return (int) $postMaxSize;
        }

        $metric = strtoupper(substr($postMaxSize, -1));
        $postMaxSize = (int) $postMaxSize;

        switch ($metric) {
            case 'K':
                return $postMaxSize * 1024;
            case 'M':
                return $postMaxSize * 1048576;
            case 'G':
                return $postMaxSize * 1073741824;
            default:
                return $postMaxSize;
        }
    }
}
