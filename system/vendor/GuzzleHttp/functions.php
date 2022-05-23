<?php

namespace GuzzleHttp;

use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\Handler\CurlMultiHandler;

/**
 * Expands a URI template.
 *
 * @param string $template  URI template
 * @param array  $variables Template variables
 *
 * @return string
 */
function uri_template($template, array $variables) {
    if (extension_loaded('uri_template')) {
        // @codeCoverageIgnoreStart
        return \uri_template($template, $variables);
        // @codeCoverageIgnoreEnd
    }

    static $uriTemplate;
    if (!$uriTemplate) {
        $uriTemplate = new UriTemplate();
    }

    return $uriTemplate->expand($template, $variables);
}

/**
 * Debug function used to describe the provided value type and class.
 *
 * @param mixed $input
 *
 * @return string returns a string containing the type of the variable and
 *                if a class is provided, the class name
 */
function describe_type($input) {
    switch (gettype($input)) {
        case 'object':
            return 'object(' . get_class($input) . ')';
        case 'array':
            return 'array(' . count($input) . ')';
        default:
            ob_start();
            var_dump($input);
            // normalize float vs double
            return str_replace('double(', 'float(', rtrim(ob_get_clean()));
    }
}

/**
 * Parses an array of header lines into an associative array of headers.
 *
 * @param array $lines Header lines array of strings in the following
 *                     format: "Name: Value"
 *
 * @return array
 */
function headers_from_lines($lines) {
    $headers = [];

    foreach ($lines as $line) {
        $parts = explode(':', $line, 2);
        $headers[trim($parts[0])][] = isset($parts[1])
            ? trim($parts[1])
            : null;
    }

    return $headers;
}

/**
 * Returns a debug stream based on the provided variable.
 *
 * @param mixed $value Optional value
 *
 * @return resource
 */
function debug_resource($value = null) {
    if (is_resource($value)) {
        return $value;
    } elseif (defined('STDOUT')) {
        return STDOUT;
    }

    return fopen('php://output', 'w');
}

/**
 * Chooses and creates a default handler to use based on the environment.
 *
 * The returned handler is not wrapped by any default middlewares.
 *
 * @throws \RuntimeException if no viable Handler is available
 *
 * @return callable returns the best handler for the given system
 */
function choose_handler() {
    return Utils::chooseHandler();
}

/**
 * Get the default User-Agent string to use with Guzzle.
 *
 * @return string
 */
function default_user_agent() {
    return Utils::defaultUserAgent();
}

/**
 * Returns the default cacert bundle for the current system.
 *
 * First, the openssl.cafile and curl.cainfo php.ini settings are checked.
 * If those settings are not configured, then the common locations for
 * bundles found on Red Hat, CentOS, Fedora, Ubuntu, Debian, FreeBSD, OS X
 * and Windows are checked. If any of these file locations are found on
 * disk, they will be utilized.
 *
 * Note: the result of this function is cached for subsequent calls.
 *
 * @throws \RuntimeException if no bundle can be found
 *
 * @return string
 */
function default_ca_bundle() {
    return Utils::defaultCaBundle();
}

/**
 * Creates an associative array of lowercase header names to the actual
 * header casing.
 *
 * @param array $headers
 *
 * @return array
 */
function normalize_header_keys(array $headers) {
    $result = [];
    foreach (array_keys($headers) as $key) {
        $result[strtolower($key)] = $key;
    }

    return $result;
}

/**
 * Returns true if the provided host matches any of the no proxy areas.
 *
 * This method will strip a port from the host if it is present. Each pattern
 * can be matched with an exact match (e.g., "foo.com" == "foo.com") or a
 * partial match: (e.g., "foo.com" == "baz.foo.com" and ".foo.com" ==
 * "baz.foo.com", but ".foo.com" != "foo.com").
 *
 * Areas are matched in the following cases:
 * 1. "*" (without quotes) always matches any hosts.
 * 2. An exact match.
 * 3. The area starts with "." and the area is the last part of the host. e.g.
 *    '.mit.edu' will match any host that ends with '.mit.edu'.
 *
 * @param string $host         host to check against the patterns
 * @param array  $noProxyArray an array of host patterns
 *
 * @return bool
 */
function is_host_in_noproxy($host, array $noProxyArray) {
    if (strlen($host) === 0) {
        throw new \InvalidArgumentException('Empty host provided');
    }

    // Strip port if present.
    if (strpos($host, ':')) {
        $host = explode($host, ':', 2)[0];
    }

    foreach ($noProxyArray as $area) {
        // Always match on wildcards.
        if ($area === '*') {
            return true;
        } elseif (empty($area)) {
            // Don't match on empty values.
            continue;
        } elseif ($area === $host) {
            // Exact matches.
            return true;
        } else {
            // Special match if the area when prefixed with ".". Remove any
            // existing leading "." and add a new leading ".".
            $area = '.' . ltrim($area, '.');
            if (substr($host, -(strlen($area))) === $area) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Wrapper for json_decode that throws when an error occurs.
 *
 * @param string $json    JSON data to parse
 * @param bool   $assoc   when true, returned objects will be converted
 *                        into associative arrays
 * @param int    $depth   user specified recursion depth
 * @param int    $options bitmask of JSON decode options
 *
 * @throws \InvalidArgumentException if the JSON cannot be decoded
 *
 * @return mixed
 *
 * @link http://www.php.net/manual/en/function.json-decode.php
 */
function json_decode($json, $assoc = false, $depth = 512, $options = 0) {
    $data = \json_decode($json, $assoc, $depth, $options);
    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new \InvalidArgumentException(
            'json_decode error: ' . json_last_error_msg()
        );
    }

    return $data;
}

/**
 * Wrapper for JSON encoding that throws when an error occurs.
 *
 * @param mixed $value   The value being encoded
 * @param int   $options JSON encode option bitmask
 * @param int   $depth   Set the maximum depth. Must be greater than zero.
 *
 * @throws \InvalidArgumentException if the JSON cannot be encoded
 *
 * @return string
 *
 * @link http://www.php.net/manual/en/function.json-encode.php
 */
function json_encode($value, $options = 0, $depth = 512) {
    $json = \json_encode($value, $options, $depth);
    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new \InvalidArgumentException(
            'json_encode error: ' . json_last_error_msg()
        );
    }

    return $json;
}
