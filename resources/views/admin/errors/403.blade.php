<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Light/Dark mode -->
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts
    @livewireStyles
</head>

<body class="w-screen h-screen flex justify-center items-center overflow-hidden bg-gray-50 dark:bg-gray-900">

    <div class="w-full h-full flex flex-col justify-center items-center text-white text-center text-2xl gap-4">
        <p class="text-4xl">
            {{__('admin.You have been logged out')}}
        </p>
        <x-admin.button type="link" color="success" href="/admin/login" class="text-white flex cursor-pointer" data-modal-target="defaultModal" data-modal-toggle="defaultModal">
            {{__('admin.Login')}}
        </x-admin.button>
    </div>
</body>

</html>