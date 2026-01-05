<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LostFound'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-800">

    @include('layouts.navigation')

    {{-- KONTEN UTAMA (NETRAL) --}}
    <main class="min-h-[calc(100vh-64px)] py-10">
        @yield('content')
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
  document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
      duration: 700,
      easing: 'ease-out',
      once: true,
      offset: 80,
    });
  });
</script>

@include('layouts.footer')
    @stack('scripts')
</body>
</html>
