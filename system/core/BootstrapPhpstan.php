<?php
define('CFPHPSTAN', 1);
define('CFCLI_APPCODE', null);
$_SERVER['APP_ENV'] = 'testing';
require realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Bootstrap.php');
