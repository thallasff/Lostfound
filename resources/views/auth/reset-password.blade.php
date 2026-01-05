@extends('layouts.guest')

@section('content')
<x-auth-card>

    <div class="text-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Buat Password Baru</h2>
        <p class="text-sm text-gray-500 mt-1">
            Isi password baru dan konfirmasi password.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
            />
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />

            <div class="relative">
                <x-text-input
                    id="password"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                />
                <button type="button"
                    id="togglePassword"
                    class="absolute inset-y-0 right-3 top-1/2 -translate-y-1/2 text-gray-600">
                    👁️
                </button>
            </div>

            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <button type="button"
                    id="togglePassword2"
                    class="absolute inset-y-0 right-3 top-1/2 -translate-y-1/2 text-gray-600">
                    👁️
                </button>
            </div>
        </div>

        <x-primary-button class="mt-6 w-full">
            {{ __('Simpan Password') }}
        </x-primary-button>

        {{-- Link ke Forgot Password (sesuai maumu: link taro di reset page aja) --}}
        <p class="text-center text-sm text-slate-400 mt-4">
            Belum dapat link?
            <a href="{{ route('password.request') }}" class="text-orange-500 hover:text-orange-300">
                Kirim ulang link reset
            </a>
        </p>

    </form>

</x-auth-card>

<script>
(function () {
  const pw = document.getElementById('password');
  const pw2 = document.getElementById('password_confirmation');
  const t1 = document.getElementById('togglePassword');
  const t2 = document.getElementById('togglePassword2');

  function toggle(input, btn) {
    if (!input || !btn) return;
    btn.addEventListener('click', () => {
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      btn.textContent = isPass ? '🙈' : '👁️';
    });
  }

  toggle(pw, t1);
  toggle(pw2, t2);
})();
</script>
@endsection
