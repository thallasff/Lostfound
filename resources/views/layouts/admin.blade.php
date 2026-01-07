<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-800">
    <div class="min-h-screen flex">

        {{-- SIDEBAR --}}
        <aside class="w-60 border-r bg-white px-4 py-6">
            <div class="flex items-center gap-3 mb-8">
                {{-- Logo placeholder --}}
                <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <span class="text-orange-600 font-bold">LF</span>
                </div>
                <div class="leading-tight">
                    <div class="font-semibold">LOSTFOUND</div>
                    <div class="text-xs text-gray-500">Admin Panel</div>
                </div>
            </div>

            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-50
                          {{ request()->routeIs('admin.dashboard') ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.verify.laporan') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-50
                          {{ request()->routeIs('admin.verify.laporan*') ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>✔</span>
                    <span>Verifikasi Kehilangan</span>
                </a>

                <a href="{{ route('admin.verify.penemuan') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-50
                          {{ request()->routeIs('admin.verify.penemuan*') ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>✔</span>
                    <span>Verifikasi Penemuan</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-50
                          {{ request()->routeIs('admin.users*') ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>👤</span>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('admin.moderate.chat') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-50
                          {{ request()->routeIs('admin.moderate.chat') ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>💬</span>
                    <span>Moderasi Chat</span>
                </a>
            </nav>
        </aside>

        {{-- MAIN --}}
        <main class="flex-1">
            {{-- TOPBAR --}}
            <header class="h-16 border-b bg-white flex items-center px-6">
                <div class="flex-1"></div>

                <h1 class="flex-1 text-center font-semibold text-gray-900">
                    @yield('page_title', 'Dashboard')
                </h1>

                <div class="flex-1 flex items-center justify-end gap-3">
                    {{-- Icon profile --}}
                    <div class="h-9 w-9 rounded-full border flex items-center justify-center">
                        <span class="text-lg">👤</span>
                    </div>
                </div>
            </header>

            <section class="p-6">
                @yield('content')
            </section>
        </main>

    </div>
</body>
</html>
