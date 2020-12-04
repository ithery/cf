<?php

/**
 * Define the user's "~/.config/devsuite" path.
 */
define('DEVSUITE_HOME_PATH', str_replace('\\', '/', $_SERVER['HOME'] . '/.config/devsuite'));
define('DEVSUITE_STATIC_PREFIX', '41c270e4-5535-4daa-b23e-c269744c2f45');
define('CFDEVSUITE',1);

/**
 * Show the Valet 404 "Not Found" page.
 */
function showDevSuite404() {
    http_response_code(404);
    require __DIR__ . '/system/data/devsuite/template/404.html';
    exit;
}

/**
 * You may use wildcard DNS providers xip.io or nip.io as a tool for testing your site via an IP address.
 * It's simple to use: First determine the IP address of your local computer (like 192.168.0.10).
 * Then simply use http://project.your-ip.xip.io - ie: http://laravel.192.168.0.10.xip.io.
 */
function devSuiteSupportWildcardDns($domain) {
    if (in_array(substr($domain, -7), ['.xip.io', '.nip.io'])) {
        // support only ip v4 for now
        $domainPart = explode('.', $domain);
        if (count($domainPart) > 6) {
            $domain = implode('.', array_reverse(array_slice(array_reverse($domainPart), 6)));
        }
    }

    if (strpos($domain, ':') !== false) {
        $domain = explode(':', $domain)[0];
    }

    return $domain;
}

/**
 * Load the Valet configuration.
 */
$devSuiteConfig = json_decode(
        file_get_contents(DEVSUITE_HOME_PATH . '/config.json'), true
);




/**
 * Parse the URI and site / host for the incoming request.
 */
$uri = urldecode(
        explode('?', $_SERVER['REQUEST_URI'])[0]
);

$siteName = basename(
        // Filter host to support wildcard dns feature
        devSuiteSupportWildcardDns($_SERVER['HTTP_HOST']), '.' . $devSuiteConfig['tld']
);

if (strpos($siteName, 'www.') === 0) {
    $siteName = substr($siteName, 4);
}

/**
 * Determine the fully qualified path to the site.
 */
$devSuiteSitePath = null;
$domain = array_slice(explode('.', $siteName), -1)[0];


foreach ($devSuiteConfig['paths'] as $path) {
    if (is_dir($path . '/' . $siteName)) {
        $devSuiteSitePath = $path . '/' . $siteName;
        break;
    }

    if (is_dir($path . '/' . $domain)) {
        $devSuiteSitePath = $path . '/' . $domain;
        break;
    }
}


if (is_null($devSuiteSitePath)) {
    showDevSuite404();
}

$devSuiteSitePath = realpath($devSuiteSitePath);


/**
 * Find the appropriate Valet driver for the request.
 */
$devSuiteDriver = null;

$driverFiles = [
    __DIR__ . '/system/libraries/CDevSuite/Trait/ConsoleTrait.php',
    __DIR__ . '/system/libraries/CDevSuite/Trait/WindowsTrait.php',
    __DIR__ . '/system/libraries/CDevSuite/Trait/MacTrait.php',
    __DIR__ . '/system/libraries/CDevSuite/Trait/LinuxTrait.php',
    __DIR__ . '/system/libraries/CDevSuite.php',
    __DIR__ . '/system/libraries/CDevSuite/DevSuiteDriver.php',
    __DIR__ . '/system/libraries/CDevSuite/Driver/BasicDevSuiteDriver.php',
];


foreach ($driverFiles as $file) {
    require $file;
}

$devSuiteDriver = CDevSuite_DevSuiteDriver::assign($devSuiteSitePath, $siteName, $uri);



if (!$devSuiteDriver) {
    showDevSuite404();
}

/*
 * Ngrok uses the X-Original-Host to store the forwarded hostname.
 */
if (isset($_SERVER['HTTP_X_ORIGINAL_HOST']) && !isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_X_FORWARDED_HOST'] = $_SERVER['HTTP_X_ORIGINAL_HOST'];
}

/**
 * Allow driver to mutate incoming URL.
 */
$uri = $devSuiteDriver->mutateUri($uri);



/**
 * Determine if the incoming request is for a static file.
 */
$isPhpFile = pathinfo($uri, PATHINFO_EXTENSION) === 'php';


if ($uri !== '/' && !$isPhpFile && $staticFilePath = $devSuiteDriver->isStaticFile($devSuiteSitePath, $siteName, $uri)) {
    return $devSuiteDriver->serveStaticFile($staticFilePath, $devSuiteSitePath, $siteName, $uri);
}

/*
 * Attempt to load server environment variables.
 */
$devSuiteDriver->loadServerEnvironmentVariables(
        $devSuiteSitePath, $siteName
);

/**
 * Attempt to dispatch to a front controller.
 */
$frontControllerPath = $devSuiteDriver->frontControllerPath(
        $devSuiteSitePath, $siteName, $uri
);



if (!$frontControllerPath) {
    showDevSuite404();
}

chdir(dirname($frontControllerPath));

require $frontControllerPath;
