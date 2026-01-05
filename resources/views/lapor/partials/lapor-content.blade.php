@extends('layouts.create')

@section('content')
<div class="min-h-screen bg-white">
    <div class="py-10">
        <div class="max-w-md mx-auto bg-[#d9d4cd] shadow-xl rounded-2xl px-10 py-8">
            <h2 class="text-center text-2xl font-extrabold mb-8">
                @yield('lapor-title')
            </h2>

            @if(session('success'))
                <div class="mb-4 text-center text-sm font-semibold text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ALERT MODAL (SUKSES / ERROR / VALIDASI) --}}
@php
    $alertTitle = null;
    $alertBody = null;
    $alertType = null; // success | error | warning

    if (session('success')) {
        $alertType = 'success';
        $alertTitle = 'Berhasil 🎉';
        $alertBody = session('success');
    } elseif (session('error')) {
        $alertType = 'error';
        $alertTitle = 'Gagal ❌';
        $alertBody = session('error');
    } elseif ($errors->any()) {
        $alertType = 'warning';
        $alertTitle = 'Form belum lengkap ⚠️';
        $alertBody = implode("\n", $errors->all());
    }
@endphp

@if($alertType)
<div
    x-data="{ open: true }"
    x-show="open"
    x-transition
    class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
>
    {{-- backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="open=false"></div>

    {{-- modal --}}
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="px-5 py-4
            @if($alertType==='success') bg-green-600
            @elseif($alertType==='error') bg-red-600
            @else bg-yellow-500 @endif
            text-white">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-lg">{{ $alertTitle }}</h3>
                <button @click="open=false" class="text-white/90 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>

        <div class="px-5 py-4">
            <div class="text-gray-800 whitespace-pre-line leading-relaxed">
                {{ $alertBody }}
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button @click="open=false"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">
                    Tutup
                </button>
                @if($alertType==='warning')
                <button @click="open=false"
                        class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold">
                    Oke, isi dulu
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

            @yield('lapor-body')
        </div>
    </div>
</div>
@endsection
