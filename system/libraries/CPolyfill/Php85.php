<?php
use Symfony\Polyfill\Php85 as p;

class CPolyfill_Php85 {
    public static function polyfill() {
        if (\PHP_VERSION_ID >= 80500) {
            return;
        }

        if (!function_exists('get_error_handler')) {
            function get_error_handler(): ?callable {
                return p\Php85::get_error_handler();
            }
        }

        if (!function_exists('get_exception_handler')) {
            function get_exception_handler(): ?callable {
                return p\Php85::get_exception_handler();
            }
        }

        if (!function_exists('array_first')) {
            function array_first(array $array) {
                return p\Php85::array_first($array);
            }
        }

        if (!function_exists('array_last')) {
            function array_last(array $array) {
                return p\Php85::array_last($array);
            }
        }
    }
}
