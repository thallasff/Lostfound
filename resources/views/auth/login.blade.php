@extends('layouts.guest')

@section('content')
<x-auth-card>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Username --}}
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input
                id="username"
                class="block mt-1 w-full"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
            />
            @error('username')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input
                    id="password"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />

                <button
                    type="button"
                    id="togglePassword"
                    class="absolute inset-y-0 right-3 flex items-center text-gray-600"
                    aria-label="toggle password"
                >
                    👁️
                </button>
            </div>

            @error('password')
                <p class="mt-1 text-sm text-red-500">
                    {{ $message }}
                    @if (Route::has('password.request'))
                    @endif
                </p>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="inline-flex items-center my-2">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                {{ old('remember') ? 'checked' : '' }}
                class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
            >
            <span class="ml-2 text-sm text-gray-700">{{ __('Remember me') }}</span>
        </label>

        <x-primary-button class="mt-4 w-full">
            {{ __('Login') }}
        </x-primary-button>

        @if (Route::has('password.request'))
    <p class="text-center text-sm text-slate-400 mt-3">
        Lupa password?
        <a href="{{ route('password.request') }}" class="underline text-orange-500 hover:text-orange-300">
            Reset
        </a>
    </p>
@endif

        <p class="text-center text-sm text-slate-400 mt-4">
            Belum punya akun?
            <a href="{{ route('register') }}" class="underline text-orange-500 hover:text-orange-300">Daftar</a>
        </p>
    </form>

</x-auth-card>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pwInput = document.getElementById('password');
    const togglePw = document.getElementById('togglePassword');

    if (!pwInput || !togglePw) return;

    togglePw.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        togglePw.textContent = isHidden ? '🙈' : '👁️';
    });
});
</script>
@endsection