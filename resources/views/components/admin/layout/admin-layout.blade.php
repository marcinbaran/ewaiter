@props(['mainClass' => ''])

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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Light/Dark mode -->
    <script>
        if (localStorage.getItem("color-theme") === "dark" || (!("color-theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    </script>
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts
    @livewireStyles
</head>

<body class="w-screen h-screen grid grid-cols-body grid-rows-body overflow-hidden bg-gray-50 dark:bg-gray-900">
<x-admin.layout.sidebar />
<x-admin.layout.nav-bar />
<div id="main-content"
     class="h-full overflow-scroll row-start-2 col-start-1 lg:col-start-2 col-span-2 lg:col-auto flex flex-col">
    <main class="grow p-4 font-['Albert_Sans'] {{ $mainClass }}" {!!$attributes!!}>
        <x-admin.flash-message />
        <x-admin.navigation />
        {{ $slot }}
    </main>
    <x-admin.layout.footer></x-admin.layout.footer>
</div>
@yield('bottomscripts')
<x-admin.firebase />
<script src="https://hammerjs.github.io/dist/hammer.js"></script>
</body>

</html>
