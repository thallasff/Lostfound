@extends('layouts.admin')

@section('title', 'Moderasi Chat')
@section('page_title', 'Moderasi Chat')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

    {{-- LEFT: list thread --}}
    <div class="md:col-span-4 bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
      <div class="p-4 border-b">
        <h2 class="font-semibold text-lg">Inbox Support</h2>
        <p class="text-sm text-gray-500">Chat dari pengguna</p>
      </div>

      <div class="divide-y">
        @php $users = $users ?? collect(); $unread = $unread ?? collect(); @endphp

        @forelse($threads as $t)
          @php
            $active = isset($thread) && $thread->id === $t->id;
            $pelaporId = ((int)$t->user_low_id === 0) ? (int)$t->user_high_id : (int)$t->user_low_id;

            $u = $users[$pelaporId] ?? null;
            $name = $u->username ?? ('Pelapor #'.$pelaporId);
            $pp = optional($u?->profile)->foto_profil;
            $avatar = $pp ? Storage::url($pp) : asset('icons/user.png');

            $badge = (int)($unread[$t->id] ?? 0);
          @endphp

          <a href="{{ route('admin.moderate.chat.show', $t->id) }}"
             class="flex items-center gap-3 p-4 hover:bg-orange-50 transition {{ $active ? 'bg-orange-50' : '' }}">

            <img src="{{ $avatar }}" class="w-11 h-11 rounded-full object-cover border border-gray-200" alt="avatar">

            <div class="min-w-0 flex-1">
              <div class="font-medium text-gray-800 truncate">{{ $name }}</div>
              <div class="text-sm text-gray-500 truncate">Klik untuk buka chat</div>
            </div>

            @if($badge > 0)
              <span class="min-w-6 h-6 px-2 rounded-full bg-orange-500 text-white text-xs flex items-center justify-center">
                {{ $badge }}
              </span>
            @endif
          </a>
        @empty
          <div class="p-6 text-gray-500 text-sm">Belum ada chat support.</div>
        @endforelse
      </div>
    </div>

    {{-- RIGHT: chat room --}}
    <div class="md:col-span-8 bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col">
      @if(isset($thread))
        <div class="p-4 border-b flex items-center justify-between">
          <div class="font-semibold text-gray-800">
            {{ $otherUser?->username ?? ('Pelapor #'.($pelaporId ?? '-')) }}
          </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto space-y-3" style="max-height: 60vh;">
          @php
  $msgs = ($messages instanceof \Illuminate\Support\Collection) ? $messages : collect($messages);
  $lastAdmin = $msgs->where('sender_pelapor_id', 0)->last();
@endphp


          @foreach($msgs as $m)
            @php
              $isAdmin = ((int)$m->sender_pelapor_id === 0);
              $isSystem = (($m->message_type ?? 'text') === 'system');
            @endphp

            <div class="flex flex-col {{ $isAdmin ? 'items-end' : 'items-start' }}">
              <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }} w-full">
                <div class="{{ $isAdmin ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-800' }}
                            px-4 py-2 rounded-2xl max-w-[75%]">
                  @if($isSystem)
                    <pre class="text-sm whitespace-pre-wrap font-mono">{{ $m->body }}</pre>
                  @else
                    <div class="text-sm whitespace-pre-wrap">{{ $m->body ?? '[kosong]' }}</div>
                  @endif

                  <div class="text-[11px] opacity-70 mt-1 text-right">
                    {{ optional($m->created_at)->format('H:i') }}
                  </div>
                </div>
              </div>
            </div>
            @if($isAdmin && $lastAdmin && $m->id === $lastAdmin->id && $m->read_at)
  <div class="text-[11px] text-gray-400 mt-1 pr-1 text-right">
    Seen {{ optional($m->read_at)->format('H:i') }}
  </div>
@endif
          @endforeach
        </div>

        <form method="POST" action="{{ route('admin.moderate.chat.send', $thread->id) }}" class="p-4 border-t flex gap-3">
          @csrf
          <input type="text" name="pesan"
                 class="flex-1 rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                 placeholder="Balas sebagai Admin..." required>

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
