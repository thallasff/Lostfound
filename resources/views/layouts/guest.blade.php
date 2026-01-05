<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'lostfound') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    {{-- Background --}}
    <div class="min-h-screen flex justify-center items-center bg-cover bg-center bg-no-repeat relative"
         style="background-image: url('{{ asset('image/bg.jpg') }}');">

        {{-- Overlay Blur Layer --}}
        <div class="absolute inset-0 bg-white/30 backdrop-blur-md"></div>

        {{-- Card Wrapper --}}
        <div class="relative z-10 w-full max-w-md">
            @yield('content')
        </div>

    </div>

</body>
</html>
