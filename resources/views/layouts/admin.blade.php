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
<aside class="w-64 bg-orange-500     text-white px-4 py-6">

    {{-- Brand --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-lg bg-white flex items-center justify-center overflow-hidden">
    <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
</div>

        <div class="leading-tight">
            <div class="font-semibold">LOSTFOUND</div>
            <div class="text-xs text-white/80">Admin Panel</div>
        </div>
    </div>

    {{-- Welcome --}}
    <div class="mb-6 rounded-xl bg-white/15 px-4 py-3">
        <div class="text-xs text-white/80">Welcome</div>
        <div class="font-semibold truncate">
            {{ auth('admin')->user()->username ?? auth('admin')->user()->nama ?? 'Admin' }}
        </div>
    </div>

    @php
        $linkBase = "flex items-center justify-between rounded-xl px-4 py-3 transition";
        $leftPart = "flex items-center gap-3 min-w-0";
        $bullet   = "text-white/90 text-lg leading-none";
        $active   = "bg-white/15";
        $idle     = "hover:bg-white/10";
        $textCls  = "font-medium truncate";
        $iconRight= "text-white/90";
    @endphp

    <nav class="space-y-2 text-sm">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="{{ $linkBase }} {{ request()->routeIs('admin.dashboard') ? $active : $idle }}">
            <div class="{{ $leftPart }}">
                <span class="{{ $bullet }}">•</span>
                <span class="{{ $textCls }}">Dashboard</span>
            </div>
            <span class="{{ $iconRight }}">🏠</span>
        </a>

        {{-- Verifikasi Kehilangan --}}
        <a href="{{ route('admin.verify.laporan') }}"
           class="{{ $linkBase }} {{ request()->routeIs('admin.verify.laporan*') ? $active : $idle }}">
            <div class="{{ $leftPart }}">
                <span class="{{ $bullet }}">•</span>
                <span class="{{ $textCls }}">Verifikasi Kehilangan</span>
            </div>
            <span class="{{ $iconRight }}">📌</span>
        </a>

        {{-- Verifikasi Penemuan --}}
        <a href="{{ route('admin.verify.penemuan') }}"
           class="{{ $linkBase }} {{ request()->routeIs('admin.verify.penemuan*') ? $active : $idle }}">
            <div class="{{ $leftPart }}">
                <span class="{{ $bullet }}">•</span>
                <span class="{{ $textCls }}">Verifikasi Penemuan</span>
            </div>
            <span class="{{ $iconRight }}">🔎</span>
        </a>

        {{-- Manajemen Pengguna --}}
        <a href="{{ route('admin.users.index') }}"
           class="{{ $linkBase }} {{ request()->routeIs('admin.users*') ? $active : $idle }}">
            <div class="{{ $leftPart }}">
                <span class="{{ $bullet }}">•</span>
                <span class="{{ $textCls }}">Manajemen Pengguna</span>
            </div>
            <span class="{{ $iconRight }}">👥</span>
        </a>

        {{-- Moderasi Chat --}}
        <a href="{{ route('admin.moderate.chat') }}"
           class="{{ $linkBase }} {{ request()->routeIs('admin.moderate.chat*') ? $active : $idle }}">
            <div class="{{ $leftPart }}">
                <span class="{{ $bullet }}">•</span>
                <span class="{{ $textCls }}">Moderasi Chat</span>
            </div>
            <span class="{{ $iconRight }}">💬</span>
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
        {{-- Dropdown Admin --}}
        <div x-data="{ open:false }" class="relative">
            <button type="button"
                    @click="open = !open"
                    class="h-10 w-10 rounded-full border border-gray-200 overflow-hidden hover:ring-2 hover:ring-orange-300 transition">
                <img src="{{ asset('image/admin.png') }}" alt="Admin" class="h-full w-full object-cover">
            </button>

            <div x-show="open"
                 x-transition
                 @click.away="open=false"
                 class="absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
                 style="display:none;">
                <div class="px-4 py-3 border-b">
                    <div class="text-sm font-semibold text-gray-800 truncate">
                        {{ auth('admin')->user()->username ?? 'admin' }}
                    </div>
                    <div class="text-xs text-gray-500 truncate">
                        {{ auth('admin')->user()->nama ?? 'Admin' }}
                    </div>
                </div>

                <a href="{{ route('admin.profile.edit') }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Profile
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Logout
                    </button>
                </form>
            </div>
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
