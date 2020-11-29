<?php

defined('SYSPATH') OR die('No direct access allowed.');
return array(
    "lang" => "id", //deprecated
    /*
      |--------------------------------------------------------------------------
      | Application Locale Configuration
      |--------------------------------------------------------------------------
      |
      | The application locale determines the default locale that will be used
      | by the translation service provider. You are free to set this value
      | to any of the locales which will be supported by the application.
      |
     */
    "locale" => "en_US",
    /*
      |--------------------------------------------------------------------------
      | Application Fallback Locale
      |--------------------------------------------------------------------------
      |
      | The fallback locale determines the locale to use when the current one
      | is not available. You may change the value to correspond to any of
      | the language folders that are provided through your application.
      |
     */
    "fallback_locale" => "en_US",
    /*
      |--------------------------------------------------------------------------
      | Application Timezone
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default timezone for your application, which
      | will be used by the PHP date and date-time functions. We have gone
      | ahead and set this to a sensible default for you out of the box.
      |
     */
    "timezone" => "Asia/Jakarta",
    /*
      |--------------------------------------------------------------------------
      | Encryption Key
      |--------------------------------------------------------------------------
      |
      | This key is used by the Illuminate encrypter service and should be set
      | to a random, 32 character string, otherwise these encrypted strings
      | will not be safe. Please do this before deploying an application!
      |
     */
    'key' => 'base64:shKObGZASSmb2lrui0DronRaSRojcXeVpKbqfNMei/o=',
    'cipher' => 'AES-256-CBC',
    "app_id" => 1,
    "install" => false,
    "title" => "CRESENITY",
    "sidebar" => true,
    "signup" => false,
    "theme" => "",
    "admin_email" => "contact@cresenitytech.com",
    "set_timezone" => true, //deprecated
    "default_timezone" => 'Asia/Jakarta', //deprecated
    "multilang" => true,
    "top_menu_cashier" => false,
    "update_last_request" => true,
    "ip_address" => "192.168.1.19",
    "code_test" => false,
    "require_js" => true,
    "merge_js" => false,
    "minify_js" => false,
    "merge_css" => false,
    "minify_css" => false,
);
