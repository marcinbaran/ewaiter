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

    <!-- Light/Dark mode -->
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
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

<body
    class="w-screen h-screen grid grid-cols-body grid-rows-body overflow-hidden bg-gray-50 dark:bg-gray-900 bg-[url('/images/placeholders/dashboard-dark.jpg')] bg-cover">
{{ $slot }}
@yield('bottomscripts')
<x-admin.firebase />
<script>
    if (document.documentElement.classList.contains("dark")) {
        document.body.classList.add("bg-[url('/images/placeholders/dashboard-dark.jpg')]");
        document.body.classList.remove("bg-[url('/images/placeholders/dashboard-light.jpg')]");
    } else {
        document.body.classList.add("bg-[url('/images/placeholders/dashboard-light.jpg')]");
        document.body.classList.remove("bg-[url('/images/placeholders/dashboard-dark.jpg')]");
    }
</script>
</body>

</html>
