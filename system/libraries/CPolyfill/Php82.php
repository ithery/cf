<?php
use Symfony\Polyfill\Php82 as p;

class CPolyfill_Php82 {
    public static function polyfill() {
        if (\PHP_VERSION_ID >= 80200) {
            return;
        }

        if (extension_loaded('odbc')) {
            if (!function_exists('odbc_connection_string_is_quoted')) {
                function odbc_connection_string_is_quoted(string $str): bool {
                    return p\Php82::odbc_connection_string_is_quoted($str);
                }
            }

            if (!function_exists('odbc_connection_string_should_quote')) {
                function odbc_connection_string_should_quote(string $str): bool {
                    return p\Php82::odbc_connection_string_should_quote($str);
                }
            }

            if (!function_exists('odbc_connection_string_quote')) {
                function odbc_connection_string_quote(string $str): string {
                    return p\Php82::odbc_connection_string_quote($str);
                }
            }
        }

        if (!function_exists('ini_parse_quantity')) {
            function ini_parse_quantity(string $shorthand): int {
                return p\Php82::ini_parse_quantity($shorthand);
            }
        }
    }
}
