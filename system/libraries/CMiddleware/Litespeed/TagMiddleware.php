<?php

class CMiddleware_Litespeed_TagMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     * @param string         $lscacheTags
     *
     * @return mixed
     */
    public function handle($request, $next, $lscacheTags = null) {
        $response = $next($request);

        $lscacheString = null;

        if (!in_array($request->getMethod(), ['GET', 'HEAD']) || !$response->getContent()) {
            return $response;
        }

        if (isset($lscacheTags)) {
            $lscacheString = str_replace(';', ',', $lscacheTags);
        }

        if (empty($lscacheString)) {
            return $response;
        }

        if ($response->headers->has('X-LiteSpeed-Tag') == false) {
            $response->headers->set('X-LiteSpeed-Tag', $lscacheString);
        }

        return $response;
    }
}
