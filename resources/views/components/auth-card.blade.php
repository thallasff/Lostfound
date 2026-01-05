{{-- resources/views/components/auth-card.blade.php --}}

<div class="w-full max-w-md p-8 bg-white rounded-xl shadow-xl border border-slate-200">

    {{-- Logo / Judul --}}
    <div class="text-center mb-6">
        <x-application-logo class="w-16 h-16 mx-auto mb-2" />
        <h1 class="text-3xl font-bold text-orange-500">{{ config('app.name') }}</h1>
        <p class="text-slate-500 text-sm mt-1">Temukan barangmu kembali</p>
    </div>

    {{ $slot }}
</div>

