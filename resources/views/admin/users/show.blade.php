@extends('layouts.admin')

@section('page-title', 'Detail Pengguna')

@section('content')
<div class="max-w-5xl mx-auto">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">{{ $user->username }}</h1>
      <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>

    <a href="{{ route('admin.users.index') }}"
       class="px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm">
      ← Kembali
    </a>
  </div>

  {{-- Card besar --}}
  <div class="bg-white rounded-3xl shadow border border-gray-100 p-6 md:p-8">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

      {{-- Avatar --}}
      <div class="md:col-span-3 flex md:block justify-center">
        @php
          $pp = optional($profile)->foto_profil;
          $avatar = $pp ? \Illuminate\Support\Facades\Storage::url($pp) : asset('icons/user.png');
        @endphp

        <div class="relative">
          <img src="{{ $avatar }}"
               alt="Foto Profil"
               class="w-24 h-24 md:w-28 md:h-28 rounded-full object-cover border-4 border-orange-400 shadow">
        </div>
      </div>

      {{-- Info --}}
      <div class="md:col-span-9">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-4 text-sm">

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">Nama</div>
            <div class="font-semibold text-gray-900">
              : {{ $profile->nama_lengkap ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">Email</div>
            <div class="font-semibold text-gray-900">
              : {{ $user->email ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">Status</div>
            <div class="font-semibold text-gray-900">
              : {{ $profile->status ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">No. Ponsel</div>
            <div class="font-semibold text-gray-900 break-all">
              : {{ $profile->no_ponsel ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">Fakultas</div>
            <div class="font-semibold text-gray-900">
              : {{ $profile->fakultas ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2">
            <div class="w-28 text-gray-500">Jurusan</div>
            <div class="font-semibold text-gray-900">
              : {{ $profile->jurusan ?? '-' }}
            </div>
          </div>

          <div class="flex gap-2 sm:col-span-2">
            <div class="w-28 text-gray-500">Mendaftar</div>
            <div class="font-semibold text-gray-900">
              : {{ optional($profile?->created_at ?? $user->created_at)->format('d-m-Y H:i') }}
            </div>
          </div>

        </div>

        {{-- Divider --}}
        <div class="my-6 border-t border-gray-100"></div>

        {{-- Tombol Hapus --}}
        <div class="flex items-center justify-center sm:justify-end gap-3">
          <form method="POST"
                action="{{ route('admin.users.destroy', $user->pelapor_id ?? $user->id) }}"
                onsubmit="return confirm('Yakin hapus akun ini? Data user & profil akan terhapus.');">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="px-8 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow hover:bg-orange-600">
              Hapus Akun
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection
