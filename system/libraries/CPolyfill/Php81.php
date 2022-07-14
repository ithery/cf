<?php
use Symfony\Polyfill\Php81 as p;

class CPolyfill_Php81 {
    public static function polyfill() {
        if (\PHP_VERSION_ID >= 80100) {
            return;
        }

        if (defined('MYSQLI_REFRESH_SLAVE') && !defined('MYSQLI_REFRESH_REPLICA')) {
            define('MYSQLI_REFRESH_REPLICA', 64);
        }

        if (!function_exists('array_is_list')) {
            /**
             * @param array $array
             *
             * @return bool
             */
            function array_is_list(array $array) {
                return p\Php81::array_is_list($array);
            }
        }

        if (!function_exists('enum_exists')) {
            /**
             * @param string $enum
             * @param bool   $autoload
             *
             * @return bool
             */
            function enum_exists($enum, $autoload = true) {
                return $autoload && class_exists($enum) && false;
            }
        }
    }
}
