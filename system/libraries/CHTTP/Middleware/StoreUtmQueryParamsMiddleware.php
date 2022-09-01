<?php
class CHTTP_Middleware_StoreUtmQueryParamsMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $utmQueryParams = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
        ];
        foreach ($utmQueryParams as $utmQueryParam) {
            if ($request->has($utmQueryParam)) {
                c::session()->put($utmQueryParam, $request->input($utmQueryParam));
            }
        }

        return $next($request);
    }
}
