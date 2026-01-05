@extends('layouts.main-web')

@section('page')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Profil</h2>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Username (readonly) --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Username</label>
                <input type="text" value="{{ $user->username }}" disabled
                    class="w-full mt-1 rounded-lg border-gray-200 bg-gray-50" />
            </div>

            {{-- Email (readonly / atau editable) --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full mt-1 rounded-lg border-gray-200 @error('email') border-red-400 @enderror" />
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6">

            {{-- Nama Lengkap --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama_lengkap"
                    value="{{ old('nama_lengkap', $profile->nama_lengkap ?? '') }}"
                    class="w-full mt-1 rounded-lg border-gray-200 @error('nama_lengkap') border-red-400 @enderror" />
                @error('nama_lengkap')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Status</label>
                <input type="text" name="status"
                    value="{{ old('status', $profile->status ?? '') }}"
                    placeholder="Mahasiswa / Dosen / Staff / Lainnya"
                    class="w-full mt-1 rounded-lg border-gray-200 @error('status') border-red-400 @enderror" />
                @error('status')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fakultas & Jurusan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold text-gray-700">Fakultas</label>
                    <input type="text" name="fakultas"
                        value="{{ old('fakultas', $profile->fakultas ?? '') }}"
                        class="w-full mt-1 rounded-lg border-gray-200 @error('fakultas') border-red-400 @enderror" />
                    @error('fakultas')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Jurusan</label>
                    <input type="text" name="jurusan"
                        value="{{ old('jurusan', $profile->jurusan ?? '') }}"
                        class="w-full mt-1 rounded-lg border-gray-200 @error('jurusan') border-red-400 @enderror" />
                    @error('jurusan')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- No HP --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">No. Ponsel</label>
                <input type="text" name="no_ponsel"
                    value="{{ old('no_ponsel', $profile->no_ponsel ?? '') }}"
                    class="w-full mt-1 rounded-lg border-gray-200 @error('no_ponsel') border-red-400 @enderror" />
                @error('no_ponsel')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Foto Profil --}}
            <div class="mb-6">
                <label class="font-semibold text-gray-700">Foto Profil</label>

                <div class="mt-2 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden border bg-gray-50 flex items-center justify-center">
                        @if(!empty($profile?->foto_profil))
                            <img src="{{ asset('storage/'.$profile->foto_profil) }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-gray-400 text-sm">No Img</span>
                        @endif
                    </div>

                    <input type="file" name="foto_profil"
                        class="block w-full text-sm text-gray-700
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:bg-orange-500 file:text-white
                               hover:file:bg-orange-600" />
                </div>

                @error('foto_profil')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('password.request') }}" class="text-sm text-orange-600 hover:underline">
                    Reset Password
                </a>

                <button type="submit"
                    class="px-6 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
