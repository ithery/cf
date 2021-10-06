<!DOCTYPE html>
<html class="no-js material-style layout-navbar-fixed layout-fixed" lang="<?php echo clang::getlang(); ?>" >
<head>
    <meta charset="utf-8">
    <title>Demo - Cresenity Framework</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ c::media('img/favico.png') }}">
    @CAppStyles
</head>
<body>
    @yield('content')
    @CAppScripts
    @stack('custom-scripts')
</body>
</html>
