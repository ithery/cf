<?php

class CMiddleware_Litespeed_CacheMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     * @param string         $lscache_control
     *
     * @return mixed
     */
    public function handle($request, $next, $lscache_control = null) {
        $response = $next($request);

        if (!in_array($request->getMethod(), ['GET', 'HEAD']) || !$response->getContent()) {
            return $response;
        }

        $esi_enabled = CF::config('lscache.esi');
        $maxage = CF::config('lscache.default_ttl', 0);
        $cacheability = CF::config('lscache.default_cacheability');
        $guest_only = CF::config('lscache.guest_only', false);

        if ($maxage === 0 && $lscache_control === null) {
            return $response;
        }

        if ($guest_only && c::auth()->check()) {
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

            return $response;
        }

        $lscache_string = "max-age=${maxage},${cacheability}";

        if (isset($lscache_control)) {
            $lscache_string = str_replace(';', ',', $lscache_control);
        }

        if (cstr::contains($lscache_string, 'esi=on') == false) {
            $lscache_string = $lscache_string . ($esi_enabled ? ',esi=on' : null);
        }

        if ($response->headers->has('X-LiteSpeed-Cache-Control') == false) {
            $response->headers->set('X-LiteSpeed-Cache-Control', $lscache_string);
        }

        return $response;
    }
}
