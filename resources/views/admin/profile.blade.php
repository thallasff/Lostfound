@extends('layouts.admin')

@section('title', 'Profile Admin')
@section('page_title', 'Profile')

@section('content')
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
      <div class="p-6 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Profile</h2>
        <p class="text-sm text-gray-500">Atur username dan nama admin.</p>
      </div>

      <div class="p-6">
        @if(session('success'))
          <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
          @csrf
          @method('PATCH')

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username"
                   value="{{ old('username', $admin->username) }}"
                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400">
            @error('username')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama"
                   value="{{ old('nama', $admin->nama) }}"
                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400">
            @error('nama')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="pt-2 flex justify-end">
            <button type="submit"
                    class="px-6 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
