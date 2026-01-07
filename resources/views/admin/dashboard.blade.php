@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('content')
    {{-- Container biar posisinya mirip mockup (card di tengah) --}}
    <div class="max-w-5xl mx-auto">

        {{-- Card besar oranye --}}
        <div class="rounded-2xl bg-[#F28C28] p-6 shadow-lg">

            {{-- 4 box statistik --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/90 rounded-xl p-4 border border-white/60 shadow-sm text-center">
                    <div class="text-xs font-semibold text-gray-700">Jumlah Laporan</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $jumlahLaporan ?? 42 }}
                    </div>
                </div>

                <div class="bg-white/90 rounded-xl p-4 border border-white/60 shadow-sm text-center">
                    <div class="text-xs font-semibold text-gray-700">Laporan Diverifikasi</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $laporanDiverifikasi ?? 30 }}
                    </div>
                </div>

                <div class="bg-white/90 rounded-xl p-4 border border-white/60 shadow-sm text-center">
                    <div class="text-xs font-semibold text-gray-700">Pengguna Aktif</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $penggunaAktif ?? 25 }}
                    </div>
                </div>

                <div class="bg-white/90 rounded-xl p-4 border border-white/60 shadow-sm text-center">
                    <div class="text-xs font-semibold text-gray-700">Chat Belum Dibalas</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $chatBelumDibalas ?? 5 }}
                    </div>
                </div>
            </div>

            {{-- Statistik laporan --}}
            <div class="mt-6">
                <h2 class="text-white font-bold text-lg mb-3">Statistik Laporan</h2>

                <div class="bg-white rounded-2xl p-4 border shadow-sm">
                    {{-- Placeholder chart (SVG) biar mirip line chart --}}
                    <div class="w-full overflow-x-auto">
                        <svg viewBox="0 0 760 220" class="w-full h-[220px]">
                            {{-- grid --}}
                            <g opacity="0.15">
                                @for ($i = 0; $i <= 10; $i++)
                                    <line x1="40" y1="{{ 20 + ($i*18) }}" x2="740" y2="{{ 20 + ($i*18) }}" stroke="black" />
                                @endfor
                            </g>

                            {{-- axes --}}
                            <line x1="40" y1="200" x2="740" y2="200" stroke="black" opacity="0.3"/>
                            <line x1="40" y1="20" x2="40" y2="200" stroke="black" opacity="0.3"/>

                            {{-- line chart --}}
                            <polyline
                                fill="none"
                                stroke="black"
                                stroke-width="3"
                                points="60,160 160,140 260,80 360,80 460,120 560,60 660,60 720,160"
                                opacity="0.8"
                            />

                            {{-- points --}}
                            @php
                                $pts = [[60,160],[160,140],[260,80],[360,80],[460,120],[560,60],[660,60],[720,160]];
                            @endphp
                            @foreach($pts as $p)
                                <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="4" fill="black" opacity="0.8"/>
                            @endforeach

                            {{-- labels --}}
                            <g font-size="12" opacity="0.75">
                                <text x="80"  y="214">Agustus</text>
                                <text x="190" y="214">September</text>
                                <text x="310" y="214">Oktober</text>
                                <text x="440" y="214">November</text>
                                <text x="610" y="214">Desember</text>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- Logout kecil (opsional, kalau kamu mau taruh di dashboard juga) --}}
        <div class="mt-4 flex justify-end">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="px-4 py-2 rounded-lg border hover:bg-gray-50">
                    Logout
                </button>
            </form>
        </div>

    </div>
@endsection
