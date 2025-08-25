<?php

use Symfony\Polyfill\Php80 as p;

class CPolyfill_Php80 {
    public static function polyfill() {
        if (\PHP_VERSION_ID >= 80000) {
            return;
        }
        if (!defined('T_NAME_QUALIFIED')) {
            define('T_NAME_QUALIFIED', -1); // nilai fiktif, agar tidak bentrok
        }
        if (!defined('T_NAME_FULLY_QUALIFIED')) {
            define('T_NAME_FULLY_QUALIFIED', -2);
        }

        if (!defined('FILTER_VALIDATE_BOOL') && defined('FILTER_VALIDATE_BOOLEAN')) {
            define('FILTER_VALIDATE_BOOL', \FILTER_VALIDATE_BOOLEAN);
        }

        if (!function_exists('fdiv')) {
            /**
             * @param float $num1
             * @param float $num2
             *
             * @return float
             */
            function fdiv($num1, $num2) {
                return p\Php80::fdiv($num1, $num2);
            }
        }
        if (!function_exists('preg_last_error_msg')) {
            /**
             * @return string
             */
            function preg_last_error_msg() {
                return p\Php80::preg_last_error_msg();
            }
        }
        if (!function_exists('str_contains')) {
            /**
             * @param null|string $haystack
             * @param null|string $needle
             *
             * @return bool
             */
            function str_contains($haystack = null, $needle = null) {
                return p\Php80::str_contains($haystack ?: '', $needle ?: '');
            }
        }
        if (!function_exists('str_starts_with')) {
            /**
             * @param null|string $haystack
             * @param null|string $needle
             *
             * @return bool
             */
            function str_starts_with($haystack = null, $needle = null) {
                return p\Php80::str_starts_with($haystack ?: '', $needle ?: '');
            }
        }
        if (!function_exists('str_ends_with')) {
            /**
             * @param null|string $haystack
             * @param null|string $needle
             *
             * @return bool
             */
            function str_ends_with($haystack = null, $needle = null) {
                return p\Php80::str_ends_with($haystack ?: '', $needle ?: '');
            }
        }
        if (!function_exists('get_debug_type')) {
            /**
             * @param mixed $value
             *
             * @return string
             */
            function get_debug_type($value) {
                return p\Php80::get_debug_type($value);
            }
        }
        if (!function_exists('get_resource_id')) {
            /**
             * @param mixed $resource
             *
             * @return int
             */
            function get_resource_id($resource) {
                return p\Php80::get_resource_id($resource);
            }
        }
    }
}
