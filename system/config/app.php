<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    /**
     * Your app name, it will used for display app identifier when running on framework.
     */
    'title' => c::env('APP_NAME', 'CRESENITY'),
    /**
     * Base path of the web site. If this includes a domain, eg: localhost/cresenity/
     * then a full URL will be used, eg: http://localhost/cresenity/. If it only includes
     * the path, and a site_protocol is specified, the domain will be auto-detected.
     */
    'site_domain' => '/',
    /**
     * Force a default protocol to be used by the site. If no site_protocol is
     * specified, then the current protocol is used, or when possible, only an
     * absolute path (with no protocol/domain) is used.
     */
    'site_protocol' => '',
    /**
     * The application prefix used by code, this is determine how the app
     * will look in libraries folder structure.
     */
    'prefix' => '',
    /**
     * The application locale determines the default locale that will be used
     * by the translation service provider. You are free to set this value
     * to any of the locales which will be supported by the application.
     */
    'locale' => 'en_US',
    /**
     * The fallback locale determines the locale to use when the current one
     * is not available. You may change the value to correspond to any of
     * the language folders that are provided through your application.
     */
    'fallback_locale' => 'en_US',
    /**
     * Here you may specify the default timezone for your application, which
     * will be used by the PHP date and date-time functions. We have gone
     * ahead and set this to a sensible default for you out of the box.
     */
    'timezone' => 'Asia/Jakarta',
    /**
     * This key is used by the encrypter service and should be set
     * to a random, 32 character string, otherwise these encrypted strings
     * will not be safe. Please do this before deploying an application!
     */
    'key' => c::env('APP_KEY', 'base64:shKObGZASSmb2lrui0DronRaSRojcXeVpKbqfNMei/o='),
    'cipher' => 'AES-256-CBC',
    /**
     * This value determines the "environment" your application is currently
     * running in. This may determine how you prefer to configure various
     * services the application utilizes. Override this in your application config file.
     */
    'environment' => c::env('ENVIRONMENT', CBase::ENVIRONMENT_DEVELOPMENT),

    /**
     * When your application is in debug mode, detailed error messages with
     * stack traces will be shown on every error that occurs within your
     * application. If disabled, a simple generic error page is shown.
     */
    'debug' => c::env('DEBUG', c::env('ENVIRONMENT') ? c::env('ENVIRONMENT') != CBase::ENVIRONMENT_PRODUCTION : !CF::isProduction()),

    'auth' => [
        'enable' => true,
        'providers' => [
            'users' => [
                'access' => [
                    'role' => [
                        'model' => CApp_Model_Roles::class,
                    ],
                    'role_nav' => [
                        'model' => CApp_Model_RoleNav::class,
                    ],
                    'role_permission' => [
                        'model' => CApp_Model_RolePermission::class,
                    ],

                ]
            ]
        ],
        'navs' => [
            'name' => 'nav',
            'renderer' => CNavigation_Renderer_SidenavRenderer::class
        ],
        'middleware' => ['web'],
        'passwords' => 'users',
        'username' => 'username',
        'email' => 'email',
        'hasher' => 'md5',
        'views' => true,
        'home' => '/home',
        'prefix' => '',
        'domain' => null,
        'limiters' => [
            'login' => null,
        ],

    ],
    'model' => [
        'org' => CApp_Model_Org::class,
        'user' => CApp_Model_Users::class,
        'role' => CApp_Model_Roles::class,
        'role_nav' => CApp_Model_RoleNav::class,
        'role_permission' => CApp_Model_RolePermission::class,
        'log_activity' => CApp_Model_LogActivity::class,
    ],
    'classes' => [
        'base' => CApp_Base::class,
        'exception_handler' => CException_ExceptionHandler::class,
    ],
    'javascript' => [
        'minify' => false,
    ],
    'lang' => 'id', //deprecated
    'app_id' => 1, //deprecated
    'install' => false, //deprecated
    'sidebar' => true, //deprecated
    'signup' => false, //deprecated
    'theme' => '',
    'admin_email' => c::env('ADMIN_EMAIL'),
    'format' => [
        'date' => c::env('FORMAT_DATE', 'Y-m-d'),
        'datetime' => c::env('FORMAT_DATETIME', 'Y-m-d H:i:s'),
        'thousand_separator' => c::env('FORMAT_THOUSAND_SEPARATOR', ','),
        'decimal_separator' => c::env('FORMAT_DECIMAL_SEPARATOR', '.'),
        'decimal_digit' => c::env('FORMAT_DECIMAL_DIGIT', 0),
        'currency_decimal_digit' => c::env('FORMAT_CURRENCY_DECIMAL_DIGIT', 0),
        'currency_prefix' => c::env('FORMAT_CURRENCY_PREFIX', ''),
        'currency_suffix' => c::env('FORMAT_CURRENCY_SUFFIX', ''),
    ],
    'smtp_host' => '', //deprecated
    'smtp_port' => '', //deprecated
    'smtp_secure' => false, //deprecated
    'smtp_username' => '', //deprecated
    'smtp_password' => '', //deprecated
    'smtp_from' => 'no-reply@core.capp', //deprecated

    'have_user_login' => true, //deprecated
    'have_user_access' => true, //deprecated
    'have_user_permission' => true, //deprecated
    'have_clock' => false, //deprecated
    'change_theme' => false, //deprecated
];
