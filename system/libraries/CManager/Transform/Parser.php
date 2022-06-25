<?php

class CManager_Transform_Parser {
    /**
     * Normalizes a legacy method so that we can accept for new method name only.
     *
     * @param string $method
     *
     * @return string
     */
    protected static function normalizeMethod($method) {
        $methodMap = [
            'format_date' => 'formatDate',
        ];

        return carr::get($methodMap, $method, $method);
    }

    /**
     * Parse a parameter list.
     *
     * @param string $method
     * @param string $parameter
     *
     * @return array
     */
    protected static function parseParameters($method, $parameter) {
        $method = strtolower($method);

        if (in_array($method, ['regex', 'not_regex', 'notregex'], true)) {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * Extract the methods name and parameters from a method.
     *
     * @param array|string $methods
     *
     * @return array
     */
    public static function parse($methods) {
        return $methods;
    }
}
