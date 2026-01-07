@extends('layouts.admin')

@section('page-title', 'Verifikasi Penemuan')

@section('content')
<div class="bg-white rounded-2xl shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Verifikasi Penemuan</h1>

    <form method="GET" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ $q ?? '' }}"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"
        placeholder="Search">
      <button class="px-3 py-2 rounded-lg bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600">
        Cari
      </button>
    </form>
  </div>

  <div class="rounded-2xl p-4">
    <div class="bg-white rounded-xl overflow-hidden border">
      <div class="max-h-[420px] overflow-y-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="p-3 text-left">Nama Barang</th>
              <th class="p-3 text-left">Penemu</th>
              <th class="p-3 text-left">Tanggal</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Detail</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @forelse($rows as $r)
              @php
                $badge = 'bg-gray-100 text-gray-700';
                $label = $r->status_verifikasi;

                $sv = strtolower(trim((string) $r->status_verifikasi));
                if ($sv === 'selesai') { $badge = 'bg-green-100 text-green-700'; }
                elseif (($r->claim_status ?? null) === 'handover_uploaded') { $badge = 'bg-yellow-100 text-yellow-700'; $label = 'Menunggu Admin'; }
                elseif ($sv === 'ditemukan') { $badge = 'bg-blue-100 text-blue-700'; }
              @endphp

              <tr>
                <td class="p-3">{{ $r->nama_barang }}</td>
                <td class="p-3">{{ $r->username_penemu }}</td>
                <td class="p-3">{{ optional($r->created_at)->format('d-m-Y') }}</td>
                <td class="p-3">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                    {{ $label }}
                  </span>
                </td>
                <td class="p-3">
                  <a href="{{ route('admin.verify.penemuan.show', $r->penemuan_id) }}"
                    class="px-4 py-1.5 rounded-full bg-orange-500 text-white text-xs font-bold hover:bg-orange-600">
                    Lihat
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

  <div class="mt-4">
    {{ $rows->links() }}
  </div>
</div>
@endsection
