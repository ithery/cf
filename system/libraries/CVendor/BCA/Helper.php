<?php

class CVendor_BCA_Helper {
    /**
     * Generate signature.
     *
     * @param string $url
     * @param string $accessToken
     * @param string $apiSecret
     * @param string $timestamp
     * @param array  $requestBody
     *
     * @return string
     */
    public static function bcaSignature(string $url, string $accessToken, string $apiSecret, string $timestamp, array $requestBody = []) {
        if (is_array($requestBody) && !empty($requestBody)) {
            $requestBody = json_encode($requestBody, JSON_UNESCAPED_SLASHES);
        } else {
            $requestBody = '';
        }

        $requestBody = hash('sha256', $requestBody);
        $stringToSign = sprintf('%s:%s:%s:%s', $url, $accessToken, $requestBody, $timestamp);

        $signature = hash_hmac('sha256', $stringToSign, $apiSecret, false);

        return $signature;
    }

    /**
     * Generate BCA timestamp.
     *
     * @return string
     */
    public static function bcaTimestamp() {
        $dateTime = new DateTime();

        return $dateTime->format('Y-m-d\TH:i:s.') . substr(microtime(), 2, 3) . $dateTime->format('P');
    }

    /**
     * Build url from parse_url. i'd say revese url.
     *
     * @param array $parts
     *
     * @return string
     */
    public static function buildUrl(array $parts) {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '')
            . ((isset($parts['user']) || isset($parts['host'])) ? '//' : '')
            . (isset($parts['user']) ? "{$parts['user']}" : '')
            . (isset($parts['pass']) ? ":{$parts['pass']}" : '')
            . (isset($parts['user']) ? '@' : '')
            . (isset($parts['host']) ? "{$parts['host']}" : '')
            . (isset($parts['port']) ? ":{$parts['port']}" : '')
            . (isset($parts['path']) ? "{$parts['path']}" : '')
            . (isset($parts['query']) ? "?{$parts['query']}" : '')
            . (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * Sorting url query.
     *
     * @param string $string
     *
     * @return string
     */
    public static function urlSortLexicographically(string $string) {
        $path = parse_url($string);

        $url_query = parse_url($string, PHP_URL_QUERY);
        $query_to_rray = parse_str($url_query, $result);
        ksort($result);

        $query_sorted = http_build_query($result);

        if ($query_sorted) {
            $path['query'] = $query_sorted;
            $reverse_url = self::buildUrl($path);

            return $reverse_url;
        }

        return $string;
    }
}
