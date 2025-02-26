<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter antialiased bg-slate-100 text-slate-600">

<main class="bg-white">

    <div class="relative flex">

        <!-- Content -->
        <div class="w-full md:w-1/2">

            <div class="min-h-screen h-full flex flex-col after:flex-1">

                <!-- Header -->
                <div class="flex-1">
                    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8">
                        <!-- Logo -->
                        <a class="block w-24 aspect-square" href="./">
                            <img src="/images/logo.svg" class="block w-full h-full" alt="E-waiter logo" />
                        </a>
                    </div>
                </div>

                <div class="w-full max-w-sm mx-auto px-4 py-8">
                    {{ $slot }}
                </div>
            </div>

        </div>

        <!-- Image -->
        <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
            <img class="object-cover object-center w-full h-full" src="{{ asset('images/auth-image.jpg') }}" width="760"
                 height="1024" alt="Authentication image" />
            <img class="absolute top-1/4 left-0 -translate-x-1/2 ml-8 hidden lg:block"
                 src="{{ asset('images/auth-decoration.png') }}" width="218" height="224"
                 alt="Authentication decoration" />
        </div>

    </div>

</main>
</body>
</html>
