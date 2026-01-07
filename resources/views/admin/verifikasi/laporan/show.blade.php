@extends('layouts.admin')

@section('page-title', 'Detail Verifikasi Kehilangan')

@section('content')
<div class="bg-white rounded-2xl shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-xl font-bold">{{ $item->nama_barang }}</h1>
      <p class="text-sm text-gray-500">Pelapor: {{ $item->username_pelapor }}</p>
    </div>

    <a href="{{ route('admin.verify.laporan') }}"
      class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm">
      ← Kembali
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- LEFT: foto laporan --}}
    <div class="lg:col-span-5">
      <div class="rounded-2xl border p-4">
        <div class="font-semibold mb-3">Foto Barang</div>

        @php
          $foto = array_filter([
            $item->foto_barang_1,
            $item->foto_barang_2,
            $item->foto_barang_3,
          ]);
        @endphp

        @if(count($foto))
          <div class="grid grid-cols-3 gap-2">
            @foreach($foto as $f)
              <img src="{{ asset('storage/'.$f) }}"
                   class="w-full h-28 object-cover rounded-xl border"
                   alt="foto">
            @endforeach
          </div>
        @else
          <div class="text-sm text-gray-500">Tidak ada foto.</div>
        @endif

        <div class="mt-4 text-sm text-gray-700 space-y-1">
          <div><span class="font-semibold">Kategori:</span> {{ $item->kategori }}</div>
          <div><span class="font-semibold">Lokasi:</span> {{ $item->lokasi_gedung ?? '-' }}</div>
          <div><span class="font-semibold">Status:</span> {{ $item->status }}</div>
        </div>
      </div>
    </div>

    {{-- RIGHT: claim + bukti --}}
    <div class="lg:col-span-7 space-y-6">

      <div class="rounded-2xl border p-4">
        <div class="font-semibold mb-2">Form Pemilik (Klaim)</div>

        @if($claim)
@php
  $fotoKlaim = array_filter([
    $claim->foto_klaim_1 ?? null,
    $claim->foto_klaim_2 ?? null,
    $claim->foto_klaim_3 ?? null,
    $claim->claimant_proof_photo ?? null, // ✅ ini yang bener
  ]);
@endphp

          <div class="text-sm text-gray-600 mb-3">
            Status claim: <span class="font-semibold">{{ $claim->status }}</span>
          </div>

          <div class="bg-gray-50 rounded-xl p-3 text-sm whitespace-pre-wrap">
            @if(is_array($form) && count($form))
              @foreach($form as $k => $v)
                <div class="mb-1">
                  <span class="font-semibold">{{ $k }}:</span>
                  <span>{{ is_array($v) ? json_encode($v) : $v }}</span>
                </div>
              @endforeach
            @else
              <span class="text-gray-500">Belum ada form_data.</span>
            @endif
          </div>

          <div class="mt-3">
            <div class="font-semibold text-sm mb-2">Bukti Pemilik</div>
            @if(count($fotoKlaim))
              <div class="grid grid-cols-3 gap-2">
                @foreach($fotoKlaim as $f)
                  <img src="{{ asset('storage/'.$f) }}"
                       class="w-full h-24 object-cover rounded-xl border"
                       alt="bukti pemilik">
                @endforeach
              </div>
            @else
              <div class="text-sm text-gray-500">Tidak ada bukti pemilik.</div>
            @endif
          </div>

        @else
          <div class="text-sm text-gray-500">Belum ada klaim untuk laporan ini.</div>
        @endif
      </div>

      <div class="rounded-2xl border p-4">
        <div class="font-semibold mb-2">Bukti Serah Terima (Penemu)</div>

@php
  $handover = $claim?->handover_proof_photo ?? null;
@endphp

@if($handover)
  <img src="{{ asset('storage/'.$handover) }}"
       class="w-full h-56 object-cover rounded-2xl border"
       alt="bukti serah terima">
@else
  <div class="text-sm text-gray-500">Belum ada bukti serah-terima.</div>
@endif

@php
  $itemStatus = strtolower(trim((string) ($item->status ?? '')));
  $isDoneItem = in_array($itemStatus, ['selesai','closed','closed_by_admin'], true);

  $claimStatus = strtolower(trim((string) ($claim->status ?? '')));
  $isDoneClaim = $claim && in_array($claimStatus, ['closed','closed_by_admin'], true);
@endphp


@if(!($isDoneItem || $isDoneClaim))
  <div class="mt-4 flex flex-wrap gap-2">
    <form method="POST" action="{{ route('admin.verify.laporan.selesai', $item->barang_id) }}">
      @csrf
      <button type="submit"
        class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700"
        onclick="return confirm('Tandai laporan ini SELESAI? Marker akan hilang dari map.')">
        Tandai Selesai
      </button>
    </form>

    <form method="POST" action="{{ route('admin.verify.laporan.rejectHandover', $item->barang_id) }}">
      @csrf
      <button type="submit"
        class="px-4 py-2 rounded-lg border border-red-400 text-red-600 text-sm font-semibold hover:bg-red-50"
        onclick="return confirm('Tolak bukti serah-terima?')">
        Tolak Bukti
      </button>
    </form>
  </div>
@endif
@if(!($isDoneItem || $isDoneClaim))
  <div class="mt-4 flex flex-wrap gap-2">
    <form ...>Tandai Selesai</form>

    @if($handover)
      <form ...>Tolak Bukti</form>
    @endif
  </div>
@endif


      </div>

    </div>
  </div>
</div>
@endsection
