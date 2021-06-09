<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', CF::getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cresenity Framework - Documentation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        @CApp('styles')
        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="antialiased language-php h-full w-full font-sans text-gray-900 antialiased">

        @CApp('content')
        @CApp('scripts')
    </body> 
</html> 
