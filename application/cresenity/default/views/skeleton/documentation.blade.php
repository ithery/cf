<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', CF::getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cresenity Framework - Documentation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">




        @CAppStyles
        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="antialiased language-php h-full w-full font-sans text-gray-900 antialiased">
        <div class="relative overflow-auto" id="docsScreen">
            <div class="relative lg:flex lg:items-start">
                <aside class="fixed top-0 bottom-0 left-0 z-20 h-full w-16 flex flex-col bg-gradient-to-b from-gray-100 to-white transition-all duration-300 overflow-hidden lg:sticky lg:w-80 lg:flex-shrink-0 lg:flex lg:justify-end lg:items-end 2xl:max-w-lg 2xl:w-full">
                    <div class="flex justify-center pt-8 sm:justify-center sm:pt-0">


                        <img src="{{ curl::base() }}application/cresenity/default/media/img/logo.png" />

                    </div>
                </aside>
            </div> 
            <section class="flex-1 pl-20 lg:pl-0">
                <div class="max-w-screen-lg px-4 sm:px-16 lg:px-24">
                    <div class="container">
                        @yield('content')
                    </div>
                </div>
                <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">


                    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">




                        <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                            <div class="text-center text-sm text-gray-500 sm:text-left">
                                <div class="flex items-center">

                                    <svg fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" stroke="currentColor" class="-mt-px w-5 h-5 text-gray-400">
                                    <path d="M18.121,9.88l-7.832-7.836c-0.155-0.158-0.428-0.155-0.584,0L1.842,9.913c-0.262,0.263-0.073,0.705,0.292,0.705h2.069v7.042c0,0.227,0.187,0.414,0.414,0.414h3.725c0.228,0,0.414-0.188,0.414-0.414v-3.313h2.483v3.313c0,0.227,0.187,0.414,0.413,0.414h3.726c0.229,0,0.414-0.188,0.414-0.414v-7.042h2.068h0.004C18.331,10.617,18.389,10.146,18.121,9.88 M14.963,17.245h-2.896v-3.313c0-0.229-0.186-0.415-0.414-0.415H8.342c-0.228,0-0.414,0.187-0.414,0.415v3.313H5.032v-6.628h9.931V17.245z M3.133,9.79l6.864-6.868l6.867,6.868H3.133z"></path>
                                    </svg>

                                    <a href="https://cresenity.com" class="ml-1 underline">
                                        Official Website
                                    </a>


                                </div>
                            </div>

                            <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                                Cresenity Framework v{{ CF::version() }} (PHP v{{ PHP_VERSION }})
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


    </body> 
</html> 
