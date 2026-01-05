@extends('layouts.guest')

@section('content')
<x-auth-card>

    <div class="text-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
        <p class="text-sm text-gray-500 mt-1">
            Masukkan email akun kamu. Nanti kami kirim link untuk buat password baru.
        </p>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button class="mt-6 w-full">
            {{ __('Kirim Link Reset') }}
        </x-primary-button>

        <p class="text-center text-sm text-slate-400 mt-4">
            Ingat password?
            <a href="{{ route('login') }}" class="underline text-orange-500 hover:text-orange-300">Login</a>
        </p>
    </form>

</x-auth-card>
@endsection
