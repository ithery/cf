<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CHTTP {
    protected static $middlewareEnabled = true;

    /**
     * @var CHTTP_Request
     */
    protected static $request;

    /**
     * @var CHTTP_Cookie
     */
    protected static $cookie;

    /**
     * @var CHTTP_Kernel
     */
    protected static $kernel;

    /**
     * @var CHTTP_Sitemap
     */
    protected static $sitemap;

    /**
     * @return CHTTP_Request
     */
    public static function request() {
        if (self::$request == null) {
            self::$request = CHTTP_Request::capture();
        }

        return self::$request;
    }

    /**
     * @param string $content
     * @param int    $status
     *
     * @return CHTTP_Response
     */
    public static function createResponse($content = '', $status = 200, array $headers = []) {
        if (CProfiler::isEnabled()) {
            $profilerHtml = CProfiler::render();
            if (stripos($content, '</body>') !== false) {
                // Closing body tag was found, insert the profiler data before it
                $content = str_ireplace('</body>', $profilerHtml . '</body>', $content);
            } else {
                // Append the profiler data to the output
                $content .= $profilerHtml;
            }
        }

        if (CF::config('core.render_stats') === true) {
            // Fetch memory usage in MB
            $memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

            // Fetch benchmark for page execution time
            $benchmark = CFBenchmark::get(SYSTEM_BENCHMARK . '_total_execution');

            // Replace the global template variables
            $content = str_replace(
                [
                    '{cf_version}',
                    '{cf_codename}',
                    '{execution_time}',
                    '{memory_usage}',
                    '{included_files}',
                ],
                [
                    CF_VERSION,
                    CF_CODENAME,
                    $benchmark['time'],
                    number_format($memory, 2) . 'MB',
                    count(get_included_files()),
                ],
                $content
            );
        }

        return new CHTTP_Response($content, $status, $headers);
    }

    public static function refresh() {
        static::$request = null;
    }

    /**
     * @return CHTTP_ResponseFactory
     */
    public static function responseFactory() {
        return CHTTP_ResponseFactory::instance();
    }

    /**
     * @return CHTTP_Redirector
     */
    public static function redirector() {
        return CHTTP_Redirector::instance();
    }

    public static function kernel() {
        if (self::$kernel == null) {
            self::$kernel = new CHTTP_Kernel();
        }

        return self::$kernel;
    }

    public static function shouldSkipMiddleware() {
        return !static::$middlewareEnabled;
    }

    /**
     * @return CHTTP_Cookie
     */
    public static function cookie() {
        if (static::$cookie == null) {
            $config = CF::config('session');

            static::$cookie = new CHTTP_Cookie();
            static::$cookie->setDefaultPathAndDomain(
                $config['path'],
                $config['domain'],
                $config['secure'],
                $config['same_site']
            );
        }

        return static::$cookie;
    }

    public static function setRequest(CHTTP_Request $request) {
        static::$request = $request;
        CRouting::urlGenerator()->setRequest(static::$request);
        if (CF::isTesting()) {
            $_POST = (array) $request->post();
            $_GET = (array) $request->query();
            $_REQUEST = array_merge($_GET, $_POST);
        }
    }

    /**
     * @return CHTTP_ResponseCache
     */
    public static function responseCache() {
        return CHTTP_ResponseCache::instance();
    }

    /**
     * @return CHTTP_Client
     */
    public static function client() {
        return CHTTP_Client::instance();
    }

    /**
     * @return CHTTP_RobotsTxt
     */
    public static function robotsTxt() {
        return CHTTP_RobotsTxt::instance();
    }

    /**
     * @return CHTTP_Sitemap
     */
    public static function sitemap() {
        if (self::$sitemap == null) {
            self::$sitemap = CHTTP_Sitemap::create();
        }

        return self::$sitemap;
    }
}
