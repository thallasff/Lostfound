@extends('layouts.main-web')

@section('page')
<div class="max-w-7xl mx-auto px-6 py-8">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

    {{-- LEFT: list thread --}}
    <div class="md:col-span-4 bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
      <div class="p-4 border-b">
        <h2 class="font-semibold text-lg">Chat</h2>
        <p class="text-sm text-gray-500">Pesan kamu</p>
      </div>

      <div class="divide-y">
        @php $users = $users ?? collect(); @endphp
        @forelse($threads as $t)
          @php
            $me = auth()->id();
            $otherId = ($me === $t->user_low_id) ? $t->user_high_id : $t->user_low_id;
            $active = isset($thread) && $thread->id === $t->id;
          @endphp

@php
  $u = $users[$otherId] ?? null;
  $name = $u->username ?? ('Pelapor #'.$otherId);
  $pp = optional($u?->profile)->foto_profil;
  $avatar = $pp ? Storage::url($pp) : asset('icons/user.png');
@endphp

<a href="{{ route('chat.show', $t->id) }}"
   class="flex items-center gap-3 p-4 hover:bg-orange-50 transition {{ $active ? 'bg-orange-50' : '' }}">

  <img src="{{ $avatar }}"
       class="w-11 h-11 rounded-full object-cover border border-gray-200"
       alt="avatar">

  <div class="min-w-0">
    <div class="font-medium text-gray-800 truncate">
      {{ $name }}
    </div>
    <div class="text-sm text-gray-500 truncate">
      Klik untuk buka chat
    </div>
  </div>
</a>

        @empty
          <div class="p-6 text-gray-500 text-sm">Belum ada chat.</div>
        @endforelse
      </div>
    </div>

    {{-- RIGHT: chat room --}}
    <div class="md:col-span-8 bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col">
@if(isset($thread))
  <div class="p-4 border-b flex items-center justify-between">
    <div class="font-semibold text-gray-800">
      {{ $otherUser?->username ?? ('Pelapor #'.($otherId ?? '-')) }} RoomChat
    </div>

    <div class="flex items-center gap-2">
      {{-- tombol form (muncul hanya kalau barang hilang) --}}
 @if(($canSendPickupForm ?? false) === true)
  <form method="POST" action="{{ route('chat.sendPickupForm', $thread->id) }}">
    @csrf
    <button type="submit"
      class="px-3 py-2 rounded-lg border border-orange-400 text-orange-600 text-xs font-bold hover:bg-orange-50">
      Form pengambilan barang
    </button>
  </form>
@endif


      {{-- menu titik tiga --}}
      <div x-data="{ openMenu:false }" class="relative">
        <button type="button"
                @click="openMenu = !openMenu"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
          ⋯
        </button>

        <div x-show="openMenu"
             @click.away="openMenu=false"
             x-transition
             class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
          <form method="POST" action="{{ route('chat.destroy', $thread->id) }}"
                onsubmit="return confirm('Hapus chat ini dari akun kamu?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
              Hapus Chat
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>


        <div class="p-4 flex-1 overflow-y-auto space-y-3" style="max-height: 60vh;">
@php
  $meId = auth()->id();
  $msgs = ($messages instanceof \Illuminate\Support\Collection) ? $messages : collect($messages);

  // pesan terakhir yang aku kirim (apa pun tipe-nya) buat Seen
  $lastMine = $msgs->where('sender_pelapor_id', $meId)->last();
@endphp

@foreach($msgs as $m)
  @php
    $isMe = ($m->sender_pelapor_id === $meId);
    $isSystem = (($m->message_type ?? 'text') === 'system');
  @endphp

  <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
      <div class="{{ $isMe ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-800' }}
                  px-4 py-2 rounded-2xl max-w-[75%]">

        @if($isSystem)
          {{-- ✅ System message tampil kayak chat biasa, tapi body pakai pre --}}
          <pre class="text-sm whitespace-pre-wrap font-mono">{{ $m->body }}</pre>

          <button type="button"
            class="mt-2 text-xs {{ $isMe ? 'text-white/90 hover:text-white' : 'text-orange-600 hover:underline' }} font-semibold"
            onclick="navigator.clipboard.writeText(@js($m->body))">
            Copy form
          </button>
        @else
          <div class="text-sm whitespace-pre-wrap">{{ $m->body ?? '[kosong]' }}</div>
        @endif

        <div class="text-[11px] opacity-70 mt-1 text-right">
          {{ optional($m->created_at)->format('H:i') }}
        </div>
      </div>
    </div>

    {{-- ✅ Seen (jalan untuk pesan terakhir yang kamu kirim, termasuk system) --}}
    @if($isMe && $lastMine && $m->id === $lastMine->id && $m->read_at)
      <div class="text-[11px] text-gray-400 mt-1 pr-1">
        Seen {{ $m->read_at->format('H:i') }}
      </div>
    @endif
  </div>
@endforeach

        </div>

<form method="POST" action="{{ route('chat.send', ['thread' => $thread->id]) }}" class="p-4 border-t flex gap-3">
    @csrf
    <input type="text" name="pesan"
           class="flex-1 rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
           placeholder="Tulis pesan..." required>

    <button type="submit" class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold">
        Kirim
    </button>
</form>

      @else
        <div class="p-10 text-center text-gray-500">
          Pilih chat di sebelah kiri untuk mulai.
        </div>
      @endif
    </div>

  </div>
</div>
@endsection
