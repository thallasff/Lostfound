<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Profile')</title>

    {{-- FONT --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans">

    {{-- NAVBAR (kalau mau sama) --}}
    @include('layouts.navigation')

    {{-- ISI HALAMAN --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
