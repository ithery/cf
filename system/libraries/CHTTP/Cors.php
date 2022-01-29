<?php

/**
 * @see https://github.com/fruitcake/laravel-cors
 */
class CHTTP_Cors {
    /**
     * @var CHTTP_Cors_CorsService
     */
    protected static $corsService;

    public static function corsService() {
        if (static::$corsService == null) {
            static::$corsService = new CHTTP_Cors_CorsService(static::corsOptions());
        }

        return static::$corsService;
    }

    /**
     * Get options for CorsService.
     *
     * @return array
     */
    protected static function corsOptions() {
        $config = CF::config('http.cors');

        if ($config['exposed_headers'] && !is_array($config['exposed_headers'])) {
            throw new \RuntimeException('CORS config `exposed_headers` should be `false` or an array');
        }

        foreach (['allowed_origins', 'allowed_origins_patterns',  'allowed_headers', 'allowed_methods'] as $key) {
            if (!is_array($config[$key])) {
                throw new \RuntimeException('CORS config `' . $key . '` should be an array');
            }
        }

        // Convert case to supported options
        $options = [
            'supportsCredentials' => $config['supports_credentials'],
            'allowedOrigins' => $config['allowed_origins'],
            'allowedOriginsPatterns' => $config['allowed_origins_patterns'],
            'allowedHeaders' => $config['allowed_headers'],
            'allowedMethods' => $config['allowed_methods'],
            'exposedHeaders' => $config['exposed_headers'],
            'maxAge' => $config['max_age'],
        ];

        // Transform wildcard pattern
        foreach ($options['allowedOrigins'] as $origin) {
            if (strpos($origin, '*') !== false) {
                $options['allowedOriginsPatterns'][] = static::convertWildcardToPattern($origin);
            }
        }

        return $options;
    }

    /**
     * Create a pattern for a wildcard, based on Str::is() from Laravel.
     *
     * @param string $pattern
     *
     * @see https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Str.php
     *
     * @return string
     */
    protected static function convertWildcardToPattern($pattern) {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return '#^' . $pattern . '\z#u';
    }
}
