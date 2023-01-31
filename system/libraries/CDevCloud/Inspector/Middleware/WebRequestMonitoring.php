<?php

use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @see CDevCloud
 */
class CDevCloud_Inspector_Middleware_WebRequestMonitoring implements TerminableInterface {
    /**
     * Handle an incoming request.
     *
     * @param CHTTP_Request $request
     * @param \Closure      $next
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (CDevCloud::inspector()->needTransaction()
            && CDevCloud_Inspector_Filters::isApprovedRequest(CF::config('devcloud.inspector.ignore_url'), $request)
            && $this->shouldRecorded($request)
        ) {
            $this->startTransaction($request);
        }

        return $next($request);
    }

    /**
     * Determine if Inspector should monitor current request.
     *
     * @param \CHTTP_Request $request
     *
     * @return bool
     */
    protected function shouldRecorded($request): bool {
        return true;
    }

    /**
     * Start a transaction for the incoming request.
     *
     * @param \CHTTP_Request $request
     */
    protected function startTransaction($request) {
        $transaction = CDevCloud::inspector()->startTransaction(
            $this->buildTransactionName($request)
        );
        if (c::auth()->check() && CF::config('devcloud.inspector.user')) {
            $transaction->withUser(c::auth()->user()->getAuthIdentifier());
        }
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param \CHTTP_Request  $request
     * @param \CHTTP_Response $response
     */
    public function terminate($request, $response) {
        if (CDevCloud::inspector()->isRecording() && CDevCloud::inspector()->hasTransaction()) {
            CDevCloud::inspector()->currentTransaction()
                ->addContext('Request Body', CDevCloud_Inspector_Filters::hideParameters(
                    $request->request->all(),
                    CF::config('devcloud.inspector.hidden_parameters')
                ))
                ->addContext('Response', [
                    'status_code' => $response->getStatusCode(),
                    'version' => $response->getProtocolVersion(),
                    'charset' => $response->getCharset(),
                    'headers' => $response->headers->all(),
                ])
                ->setResult($response->getStatusCode());
        }
    }

    /**
     * Generate readable name.
     *
     * @param \CHTTP_Request $request
     *
     * @return string
     */
    protected function buildTransactionName($request) {
        $route = $request->route();

        if ($route instanceof CRouting_Route) {
            $uri = $request->route()->uri();
        } else {
            $array = explode('?', $_SERVER['REQUEST_URI']);
            $uri = array_shift($array);
        }

        return $request->method() . ' ' . $this->normalizeUri($uri);
    }

    /**
     * Normalize URI string.
     *
     * @param $uri
     *
     * @return string
     */
    protected function normalizeUri($uri) {
        return '/' . trim($uri, '/');
    }
}
