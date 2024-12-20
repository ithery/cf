<?php
use Symfony\Polyfill\Php84 as p;

class CPolyfill_Php84 {
    public static function polyfill() {
        if (\PHP_VERSION_ID >= 80400) {
            return;
        }

        if (!function_exists('array_find')) {
            function array_find(array $array, callable $callback) {
                return p\Php84::array_find($array, $callback);
            }
        }

        if (!function_exists('array_find_key')) {
            function array_find_key(array $array, callable $callback) {
                return p\Php84::array_find_key($array, $callback);
            }
        }

        if (!function_exists('array_any')) {
            function array_any(array $array, callable $callback): bool {
                return p\Php84::array_any($array, $callback);
            }
        }

        if (!function_exists('array_all')) {
            function array_all(array $array, callable $callback): bool {
                return p\Php84::array_all($array, $callback);
            }
        }

        if (extension_loaded('mbstring')) {
            if (!function_exists('mb_ucfirst')) {
                function mb_ucfirst($string, ?string $encoding = null): string {
                    return p\Php84::mb_ucfirst($string, $encoding);
                }
            }

            if (!function_exists('mb_lcfirst')) {
                function mb_lcfirst($string, ?string $encoding = null): string {
                    return p\Php84::mb_lcfirst($string, $encoding);
                }
            }

            if (!function_exists('mb_trim')) {
                function mb_trim(string $string, ?string $characters = null, ?string $encoding = null): string {
                    return p\Php84::mb_trim($string, $characters, $encoding);
                }
            }

            if (!function_exists('mb_ltrim')) {
                function mb_ltrim(string $string, ?string $characters = null, ?string $encoding = null): string {
                    return p\Php84::mb_ltrim($string, $characters, $encoding);
                }
            }

            if (!function_exists('mb_rtrim')) {
                function mb_rtrim(string $string, ?string $characters = null, ?string $encoding = null): string {
                    return p\Php84::mb_rtrim($string, $characters, $encoding);
                }
            }
        }
    }
}
