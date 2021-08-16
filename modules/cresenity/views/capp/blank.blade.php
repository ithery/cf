<?php
defined('SYSPATH') or die('No direct access allowed.');

?>
<!DOCTYPE html>
<html class="no-js material-style" lang="{{ str_replace('_', '-', CF::getLocale()) }}" >
    <head>
        <meta charset="utf-8">
        <title>@CAppPageTitle</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="">
        @CAppStyles
    </head>
    <body>

        @CAppContent

        @CAppScripts
    </body>
</html>
