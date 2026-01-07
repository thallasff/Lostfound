@extends('layouts.admin')

@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="bg-white rounded-2xl shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Manajemen Pengguna</h1>

    <form method="GET" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ $q ?? '' }}"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"
        placeholder="Cari username/email/nama...">
      <button class="px-3 py-2 rounded-lg bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600">
        Cari
      </button>
    </form>
  </div>

  <div class="bg-white rounded-xl overflow-hidden border">
    <div class="max-h-[520px] overflow-y-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 sticky top-0">
          <tr>
            <th class="p-3 text-left">Username</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Mendaftar</th>
            <th class="p-3 text-left">Detail</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($rows as $u)
            @php
              $profile = $u->profile;
              $status = $profile->status ?? '-';

              // sesuai request kamu: pakai created_at dari tabel profile (kalau ada)
              $created = $profile?->created_at ?? $u->created_at ?? null;

              $badge = 'bg-gray-100 text-gray-700';
              if (strtolower($status) === 'aktif') $badge = 'bg-green-100 text-green-700';
              if (strtolower($status) === 'nonaktif') $badge = 'bg-red-100 text-red-700';
            @endphp

            <tr>
              <td class="p-3 font-medium text-gray-900">{{ $u->username }}</td>
              <td class="p-3 text-gray-700">{{ $u->email }}</td>
              <td class="p-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                  {{ $status }}
                </span>
              </td>
              <td class="p-3 text-gray-700">
                {{ $created ? $created->format('d-m-Y H:i') : '-' }}
              </td>
              <td class="p-3">
                <a href="{{ route('admin.users.show', $u->pelapor_id) }}"
                   class="px-4 py-1.5 rounded-full bg-orange-500 text-white text-xs font-bold hover:bg-orange-600">
                  Lihat
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="p-6 text-center text-gray-500">Belum ada pengguna.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4">
    {{ $rows->links() }}
  </div>
</div>
@endsection
