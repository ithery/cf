<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    /**
     * Domain, to restrict the cookie to a specific website domain. For security,
     * you are encouraged to set this option. An empty setting allows the cookie
     * to be read by any website domain.
     */
    'domain' => '',
    /**
     * Restrict cookies to a specific path, typically the installation directory.
     */
    'path' => '/',
    /**
     * Lifetime of the cookie. A setting of 0 makes the cookie active until the
     * users browser is closed or the cookie is deleted.
     */
    'expire' => 0,
    /**
     * Enable this option to only allow the cookie to be read when using the a
     * secure protocol.
     */
    'secure' => false,
    /**
     * Enable this option to disable the cookie from being accessed when using a
     * secure protocol. This option is only available in PHP 5.2 and above.
     */
    'httponly' => false,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" since this is a secure default value.
    |
    | Supported: "lax", "strict", "none", null
    |
    */
    'same_site' => 'lax',
];
