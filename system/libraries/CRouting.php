<?php

defined('SYSPATH') or die('No direct access allowed.');

class CRouting {
    /**
     * @return CRouting_UrlGenerator
     */
    public static function urlGenerator() {
        return CRouting_UrlGenerator::instance();
    }

    /**
     * @return CRouting_Router
     */
    public static function router() {
        return CRouting_Router::instance();
    }

    /**
     * @return CRouting_Factory
     */
    public static function factory() {
        return CRouting_Factory::instance();
    }

    public static function findUri() {
        if (CF::isTesting()) {
            return ltrim(c::url()->getRequest()->path(), '/');
        }
        $currentUri = '';
        if (PHP_SAPI === 'cli') {
            if (defined('CFCLI')) {
                $currentUri = '';
            } else {
                // Command line requires a bit of hacking
                if (isset($_SERVER['argv'][1])) {
                    $currentUri = $_SERVER['argv'][1];

                    // Remove GET string from segments
                    if (($query = strpos($currentUri, '?')) !== false) {
                        list($currentUri, $query) = explode('?', $currentUri, 2);

                        // Parse the query string into $_GET
                        parse_str($query, $_GET);

                        // Convert $_GET to UTF-8
                        $_GET = CUTF8::clean($_GET);
                    }
                }
            }
        } elseif (isset($_GET['cfUri'])) {
            // Use the URI defined in the query string
            $currentUri = $_GET['cfUri'];

            // Remove the URI from $_GET
            unset($_GET['cfUri']);

            // Remove the URI from $_SERVER['QUERY_STRING']
            $_SERVER['QUERY_STRING'] = preg_replace('~\cfUri\b[^&]*+&?~', '', $_SERVER['QUERY_STRING']);
        } elseif (isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO']) {
            $currentUri = $_SERVER['PATH_INFO'];
            if ($currentUri == '/404.shtml' || $currentUri == '404.shtml') {
                $currentUri = $_SERVER['REQUEST_URI'];
            }
            if ($currentUri == '/403.shtml') {
                $currentUri = $_SERVER['REQUEST_URI'];
            }
            if ($currentUri == '/500.shtml') {
                if (isset($_SERVER['REDIRECT_REDIRECT_SCRIPT_URL'])) {
                    $currentUri = $_SERVER['REDIRECT_REDIRECT_SCRIPT_URL'];
                }
                if (isset($_SERVER['REDIRECT_REDIRECT_REDIRECT_QUERY_STRING'])) {
                    $_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_REDIRECT_REDIRECT_QUERY_STRING'];
                }
                if (isset($_SERVER['REDIRECT_REDIRECT_QUERY_STRING'])) {
                    $_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_REDIRECT_QUERY_STRING'];
                }
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO']) and $_SERVER['ORIG_PATH_INFO'] and $_SERVER['ORIG_PATH_INFO'] != '/403.shtml') {
            $currentUri = $_SERVER['ORIG_PATH_INFO'];
        } elseif (isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI']) {
            $currentUri = $_SERVER['REQUEST_URI'];
            $currentUri = strtok($currentUri, '?');
        } elseif (isset($_SERVER['PHP_SELF']) and $_SERVER['PHP_SELF']) {
            $currentUri = $_SERVER['PHP_SELF'];
        }

        if (($strpos_fc = strpos($currentUri, CFINDEX)) !== false) {
            // Remove the front controller from the current uri
            $currentUri = (string) substr($currentUri, $strpos_fc + strlen(CFINDEX));
        }

        // Remove slashes from the start and end of the URI
        $currentUri = trim($currentUri, '/');

        if ($currentUri !== '') {
            // Reduce multiple slashes into single slashes
            $currentUri = preg_replace('#//+#', '/', $currentUri);
        }

        return $currentUri;
    }
}
