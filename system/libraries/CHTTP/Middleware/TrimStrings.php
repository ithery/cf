<?php

class CHTTP_Middleware_TrimStrings extends CHTTP_Middleware_TransformRequest {
    /**
     * All of the registered skip callbacks.
     *
     * @var array
     */
    protected static $skipCallbacks = [];

    /**
     * The attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [

    ];

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     *
     * @return mixed
     */
    public function handle($request, $next) {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function transform($key, $value) {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Register a callback that instructs the middleware to be skipped.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function skipWhen($callback) {
        static::$skipCallbacks[] = $callback;
    }
}
