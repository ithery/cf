<?php

return [
    /**
     * Enable ESI.
     */
    'esi' => c::env('LSCACHE_ESI_ENABLED', false),

    /**
     * Default cache TTL in seconds.
     */
    'default_ttl' => c::env('LSCACHE_DEFAULT_TTL', 0),

    /**
     * Default cache storage
     * private,no-cache,public,no-vary.
     */
    'default_cacheability' => c::env('LSCACHE_DEFAULT_CACHEABILITY', 'no-cache'),

    /**
     * Guest only mode (Do not cache logged in users).
     */
    'guest_only' => c::env('LSCACHE_GUEST_ONLY', false),
];
