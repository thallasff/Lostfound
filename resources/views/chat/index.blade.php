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
              $lastotherId = ($me === $t->user_low_id) ? $t->user_high_id : $t->user_low_id;
              $active = isset($thread) && $thread->id === $t->id;
            @endphp

  @php
    $u = $users[$lastotherId] ?? null;

    if ((int)$lastotherId === 0) {
        $name = 'Admin (Support)';
        $avatar = asset('icons/admin.png'); // boleh ganti user.png kalau belum ada
    } else {
        $name = $u->username ?? ('Pelapor #'.$lastotherId);
        $pp = optional($u?->profile)->foto_profil;
        $avatar = $pp ? Storage::url($pp) : asset('icons/user.png');
    }
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

    {{-- HEADER --}}
    <div class="p-4 border-b flex items-center justify-between">
      <div class="font-semibold text-gray-800">
        @if((int)($otherId ?? -1) === 0)
          Admin
        @else
          {{ $otherUser?->username ?? ('Pelapor #'.($otherId ?? '-')) }}
        @endif
        <span class="ml-1 text-gray-400 font-normal">RoomChat</span>
      </div>

      <div class="flex items-center gap-2">

        {{-- A) Penemu kirim template form (hanya kalau belum ada claim aktif) --}}
        @if(($canSendPickupForm ?? false) === true && empty($activeClaim))
          <form method="POST" action="{{ route('claim.sendForm', $thread->id) }}">
            @csrf
            <button type="submit"
              class="px-3 py-2 rounded-lg bg-orange-500 text-white text-xs font-bold hover:bg-orange-600">
              Kirim Form Pengambilan
            </button>
          </form>
        @endif

        {{-- MENU ⋯ + MODAL --}}
        <div x-data="{ openMenu:false, openAdmin:false }" class="relative">
          <button type="button"
                  @click="openMenu = !openMenu"
                  class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
            ⋯
          </button>

          {{-- dropdown --}}
          <div x-show="openMenu"
               @click.away="openMenu=false"
               x-transition
               class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
               style="display:none;">

            {{-- Kirim ke Admin (hanya penemu + claim approved) --}}
            @if(($activeClaim ?? null)
    && (int)auth()->id() === (int)($activeClaim->owner_id ?? 0)
    && !in_array($activeClaim->status ?? null, ['closed','closed_by_admin'], true))
  <button type="button"
          @click="openAdmin=true; openMenu=false"
          class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700">
    Kirim ke Admin
  </button>
@endif

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

          {{-- Modal Kirim ke Admin --}}
          @if(($activeClaim ?? null))
            <div x-show="openAdmin" x-transition
                 class="fixed inset-0 z-[999] flex items-center justify-center"
                 style="display:none;">
              <div class="absolute inset-0 bg-black/40" @click="openAdmin=false"></div>

              <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl p-5 mx-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="font-semibold text-gray-800">Kirim Bukti ke Admin</div>
                  <button type="button"
                          class="text-gray-500 hover:text-gray-800"
                          @click="openAdmin=false">✕</button>
                </div>

                <form method="POST"
      action="{{ route('claim.submitToAdmin', $activeClaim->id) }}"
      enctype="multipart/form-data"
      class="space-y-3">
  @csrf

  <textarea name="form_text"
    class="w-full rounded-xl border border-gray-200 p-3"
    placeholder="Paste form yang sudah diisi (final)..." required></textarea>

  <div>
    <div class="text-xs text-gray-600 mb-1">Foto bukti serah-terima (wajib)</div>
    <input type="file" name="foto_serah_terima" accept="image/*" required>
  </div>

  <div class="flex justify-end gap-2 pt-1">
    <button type="button"
            class="px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50"
            @click="openAdmin=false">
      Batal
    </button>
    <button type="submit"
            class="px-4 py-2 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600">
      Kirim
    </button>
  </div>
</form>


                <p class="text-xs text-gray-500 mt-3">
                  Setelah dikirim, admin akan verifikasi lalu menandai selesai.
                </p>
              </div>
            </div>
          @endif
        </div>

      </div>
    </div>

    {{-- MESSAGES --}}
    <div class="p-4 flex-1 overflow-y-auto space-y-3" style="max-height: 60vh;">
      @php
        $meId = auth()->id();
        $msgs = ($messages instanceof \Illuminate\Support\Collection) ? $messages : collect($messages);
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
                <pre class="text-sm whitespace-pre-wrap font-mono">{{ $m->body }}</pre>

                <button type="button"
                  class="mt-2 text-xs {{ $isMe ? 'text-white/90 hover:text-white' : 'text-orange-600 hover:underline' }} font-semibold"
                  onclick="navigator.clipboard.writeText(@js($m->body))">
                  Copy form
                </button>
              @else
                @php $text = $m->body ?? $m->pesan ?? '[kosong]'; @endphp
<div class="text-sm whitespace-pre-wrap">{{ $text }}</div>
              @endif

              <div class="text-[11px] opacity-70 mt-1 text-right">
                {{ optional($m->created_at)->format('H:i') }}
              </div>
            </div>
          </div>

          @if($isMe && $lastMine && $m->id === $lastMine->id && $m->read_at)
            <div class="text-[11px] text-gray-400 mt-1 pr-1">
              Seen {{ $m->read_at->format('H:i') }}
            </div>
          @endif
        </div>
      @endforeach
    </div>

    {{-- INPUT CHAT --}}
    <form method="POST" action="{{ route('chat.send', ['thread' => $thread->id]) }}" class="p-4 border-t flex gap-3">
      @csrf

      <textarea name="pesan"
        rows="1"
        class="flex-1 resize-none rounded-xl border border-gray-200 px-4 py-3
               focus:outline-none focus:ring-2 focus:ring-orange-400"
        placeholder="Tulis pesan..."
        oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
        onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.submit();}"
        required></textarea>

      <button type="submit"
        class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold">
        Kirim
      </button>
    </form>

  @else
    <div class="p-10 text-center text-gray-500">
      Pilih chat di sebelah kiri untuk mulai.
    </div>
  @endif
</div>
  @endsection
