@extends('layouts.guest')

@section('content')
<x-auth-card>

    {{-- kalau kamu punya session status --}}
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
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
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button class="mt-4 w-full">
            {{ __('Login') }}
        </x-primary-button>
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
