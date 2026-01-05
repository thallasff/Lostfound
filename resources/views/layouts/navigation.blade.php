<nav x-data="{ open: false }" class="bg-white">
    @php
        $homeHref = auth()->check() ? route('dashboard') : route('home');
        $isHomeActive = auth()->check()
            ? request()->routeIs('dashboard')
            : request()->routeIs('home');
    @endphp

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            {{-- LOGO --}}
            <div class="flex flex-col items-center leading-tight">
                <a href="{{ $homeHref }}" class="flex flex-col items-center">
                    <img src="{{ asset('image/Logo.png') }}" class="h-28 object-contain" alt="Logo">
                </a>
            </div>

            {{-- NAV + AUTH SECTION (DESKTOP) --}}
            <div class="hidden md:flex items-center space-x-8 ml-auto">

                {{-- MENU NAV --}}
                <div class="flex space-x-4 items-center">

                    {{-- HOME --}}
                    <a href="{{ $homeHref }}"
                       class="{{ $isHomeActive ? 'text-orange-600 bg-orange-50' : 'text-gray-700' }}
                              px-3 py-2 rounded-md hover:bg-orange-100 hover:text-orange-600 transition">
                        Home
                    </a>

                    {{-- CHAT (login aja, kalau mau guest juga boleh hapus @auth) --}}
<a href="{{ auth()->check() ? route('chat.index') : route('login') }}"
   class="{{ request()->routeIs('chat.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700' }}
          px-3 py-2 rounded-md hover:bg-orange-100 hover:text-orange-600 transition">
    Chat
</a>


                    {{-- LAPORKAN BARANG (DROPDOWN) --}}
@if(auth()->check())
    <div x-data="{ reportOpen: false }" class="relative">
        <button
            type="button"
            @click="reportOpen = !reportOpen"
            @click.away="reportOpen = false"
            class="{{ request()->routeIs('lost.create') || request()->routeIs('found.create') ? 'text-orange-600 bg-orange-50' : 'text-gray-700' }}
                   px-3 py-2 rounded-md hover:bg-orange-100 hover:text-orange-600 transition
                   flex items-center gap-2"
        >
            Laporkan Barang
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            x-show="reportOpen"
            x-transition
            class="absolute left-0 mt-2 w-56 bg-white shadow-lg rounded-xl py-2 z-50 border border-gray-200"
        >
            <a href="{{ route('found.create', ['jenis' => 'temuan']) }}"
               class="block px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg mx-2 transition">
                Penemuan Barang
            </a>

            <a href="{{ route('lost.create', ['jenis' => 'hilang']) }}"
               class="block px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg mx-2 transition">
                Kehilangan Barang
            </a>
        </div>
    </div>
@else
    <a href="{{ route('login') }}"
       class="text-gray-700 px-3 py-2 rounded-md hover:bg-orange-100 hover:text-orange-600 transition">
        Laporkan Barang
    </a>
@endif

                </div>

                {{-- AUTH SECTION (DESKTOP) --}}
                <div class="hidden md:flex items-center space-x-6">
                    <div x-data="{ profileOpen: false }" class="relative">
@php
    $user = auth()->user();
    $pp = $user?->profile?->foto_profil; // ganti nama kolom kalau beda
    $avatar = $pp ? \Illuminate\Support\Facades\Storage::url($pp) : asset('icons/user.png');
    $v = $user?->profile?->updated_at?->timestamp ?? time();
@endphp

<button @click="profileOpen = !profileOpen" class="flex items-center">
    <img src="{{ $avatar }}?v={{ $v }}"
         class="w-8 h-8 rounded-full border border-gray-300 object-cover"
         alt="profile icon">
</button>


                        <div x-show="profileOpen"
                             @click.away="profileOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-44 bg-white shadow-lg rounded-xl py-2 z-50 border border-gray-200">

                            @guest
                                <a href="{{ route('login') }}"
                                   class="block px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg mx-2 transition">
                                    Login
                                </a>
                                <a href="{{ route('register') }}"
                                   class="block px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg mx-2 transition">
                                    Register
                                </a>
                            @endguest

@auth
    <p class="px-4 py-2 text-sm text-gray-500 mb-1">
        {{ Auth::user()->username ?? Auth::user()->name }}
    </p>

    <a href="{{ route('profile.edit') }}"
       class="block mx-2 px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
        Profile
    </a>

    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
        @csrf
        <button type="submit"
                @click="profileOpen=false"
                class="block w-full text-left mx-2 px-4 py-2 text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-lg transition bg-transparent">
            Logout
        </button>
    </form>
@endauth

                        </div>
                    </div>
                </div>
            </div>

            {{-- MOBILE BUTTON (hamburger) --}}
            <div class="md:hidden">
                <button @click="open = !open">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE NAV --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden px-4 pb-4">

        {{-- HOME --}}
        <a href="{{ $homeHref }}" class="block py-2 text-gray-700 hover:text-orange-600">
            Home
        </a>

        {{-- CHAT --}}
<a href="{{ auth()->check() ? route('chat.index') : route('login') }}"
   class="{{ request()->routeIs('chat.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700' }}
          px-3 py-2 rounded-md hover:bg-orange-100 hover:text-orange-600 transition">
    Chat
</a>


        {{-- LAPORKAN BARANG (dropdown mobile) --}}
@if(auth()->check())
    <div x-data="{ mobileReportOpen: false }" class="py-2">
        <button
            type="button"
            @click="mobileReportOpen = !mobileReportOpen"
            class="w-full flex items-center justify-between text-gray-700 hover:text-orange-600"
        >
            <span>Laporkan Barang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="mobileReportOpen" x-transition class="mt-2 pl-4 space-y-2">
            <a href="{{ route('found.create', ['jenis' => 'temuan']) }}"
               class="block text-gray-700 hover:text-orange-600">
                Penemuan Barang
            </a>
            <a href="{{ route('lost.create', ['jenis' => 'hilang']) }}"
               class="block text-gray-700 hover:text-orange-600">
                Kehilangan Barang
            </a>
        </div>
    </div>
@else
    <a href="{{ route('login') }}" class="block py-2 text-gray-700 hover:text-orange-600">
        Laporkan Barang
    </a>
@endif


        {{-- AUTH (mobile) --}}
        @guest
            <a href="{{ route('login') }}" class="block py-2 text-gray-700 hover:text-orange-600">Login</a>
            <a href="{{ route('register') }}" class="block py-2 text-orange-600 font-semibold">Register</a>
        @endguest

        @auth
            <a href="{{ route('profile.edit') }}" class="block py-2 text-gray-700 hover:text-orange-600">
                Profile
            </a>
<form method="POST" action="{{ route('logout') }}" class="mt-1">
    @csrf
    <button type="submit" class="block w-full text-left py-2 text-gray-700 hover:text-orange-600">
        Logout
    </button>
</form>
        @endauth
    </div>
</nav>
