<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\BarangHilang;
use App\Models\Temuan;
use App\Models\UserPelapor; // ✅ penting
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
{
    $uid = auth()->id();

    $threads = ChatThread::query()
        ->where(function ($q) use ($uid) {
            $q->where(function ($qq) use ($uid) {
                $qq->where('user_low_id', $uid)
                   ->whereNull('deleted_low_at');
            })->orWhere(function ($qq) use ($uid) {
                $qq->where('user_high_id', $uid)
                   ->whereNull('deleted_high_at');
            });
        })
        ->orderByDesc('last_message_at')
        ->get();

    $otherIds = $threads->map(function ($t) use ($uid) {
        return ($uid == $t->user_low_id) ? $t->user_high_id : $t->user_low_id;
    })->unique()->values();

    $users = UserPelapor::with('profile')
        ->whereIn('pelapor_id', $otherIds)
        ->get()
        ->keyBy('pelapor_id');

    return view('chat.index', compact('threads', 'users'));
}

public function show(ChatThread $thread)
{
$uid = auth()->id();

if (!in_array($uid, [$thread->user_low_id, $thread->user_high_id])) abort(403);

if (
    ($uid == $thread->user_low_id && $thread->deleted_low_at) ||
    ($uid == $thread->user_high_id && $thread->deleted_high_at)
) abort(404);

    // ✅ tandain semua pesan dari lawan sebagai "seen"
    ChatMessage::where('thread_id', $thread->id)
        ->whereNull('read_at')
        ->where('sender_pelapor_id', '!=', $uid)
        ->update(['read_at' => now()]);

$threads = ChatThread::query()
    ->where(function ($q) use ($uid) {
        $q->where(function ($qq) use ($uid) {
            $qq->where('user_low_id', $uid)->whereNull('deleted_low_at');
        })->orWhere(function ($qq) use ($uid) {
            $qq->where('user_high_id', $uid)->whereNull('deleted_high_at');
        });
    })
    ->orderByDesc('last_message_at')
    ->get();


    $messages = ChatMessage::where('thread_id', $thread->id)
        ->orderBy('created_at', 'asc')
        ->get();

    $otherId = ($uid == $thread->user_low_id) ? $thread->user_high_id : $thread->user_low_id;

    $otherIds = $threads->map(function ($t) use ($uid) {
        return ($uid == $t->user_low_id) ? $t->user_high_id : $t->user_low_id;
    })->unique()->values();

    $users = UserPelapor::with('profile')
        ->whereIn('pelapor_id', $otherIds)
        ->get()
        ->keyBy('pelapor_id');

    $otherUser = $users[$otherId] ?? UserPelapor::with('profile')->where('pelapor_id', $otherId)->first();
    $canSendPickupForm = $this->isFinderUser($thread, $uid);

    return view('chat.index', compact(
    'threads', 'thread', 'messages', 'users', 'otherUser', 'otherId', 'canSendPickupForm'
));
}

    public function send(Request $request, ChatThread $thread)
    {
        $uid = auth()->id();
        if (!in_array($uid, [$thread->user_low_id, $thread->user_high_id])) abort(403);

        $data = $request->validate([
            'pesan' => 'required|string|max:2000'
        ]);

        ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_pelapor_id' => $uid,
            'message_type' => 'text',
            'body' => $data['pesan'],
        ]);

$thread->update([
    'last_message_at' => now(),
    'deleted_low_at'  => null,
    'deleted_high_at' => null,
]);



        return back();
    }

    public function startFromItem(string $type, string $id)
{
    $uid = auth()->id();

    if (!in_array($type, ['hilang', 'temuan'], true)) abort(404);
    if (!ctype_digit($id)) abort(404);
    $id = (int) $id;

    // cari pemilik laporan (lawan chat)
    if ($type === 'hilang') {
        $item = BarangHilang::where('barang_id', $id)->firstOrFail();
        $otherId = (int) $item->pelapor_id;
    } else {
        $item = Temuan::where('penemuan_id', $id)->firstOrFail();
        $otherId = (int) $item->pelapor_id;
    }

    if ($otherId === $uid) {
        return redirect()->route('chat.index')->with('info', 'Itu laporan kamu sendiri.');
    }

    $low  = min($uid, $otherId);
    $high = max($uid, $otherId);

    $thread = ChatThread::firstOrCreate(
        [
            'barang_type'  => $type,
            'barang_id'    => $id,
            'user_low_id'  => $low,
            'user_high_id' => $high,
        ],
        [
            'last_message_at' => now(),
        ]
    );

    // selalu “balikin” chat kalau pernah dihapus
    $thread->update([
        'deleted_low_at'  => null,
        'deleted_high_at' => null,
    ]);

    return redirect()->route('chat.show', $thread->id);
}


public function claimFromItem(Request $request, string $type, string $id)
{
    $uid = auth()->id();

    if (!in_array($type, ['hilang', 'temuan'], true)) abort(404);
    if (!ctype_digit($id)) abort(404);
    $id = (int) $id;

    if ($type === 'hilang') {
        $item = BarangHilang::where('barang_id', $id)->firstOrFail();
        $otherId = (int) $item->pelapor_id;
        $autoText = 'Saya menemukan barang ini';
    } else {
        $item = Temuan::where('penemuan_id', $id)->firstOrFail();
        $otherId = (int) $item->pelapor_id;
        $autoText = 'Ini barang saya';
    }

    if ($otherId === $uid) {
        return redirect()->route('chat.index')->with('info', 'Itu laporan kamu sendiri.');
    }

    $low  = min($uid, $otherId);
    $high = max($uid, $otherId);

    $thread = ChatThread::firstOrCreate(
        [
            'barang_type'  => $type,
            'barang_id'    => $id,
            'user_low_id'  => $low,
            'user_high_id' => $high,
        ],
        [
            'last_message_at' => now(),
        ]
    );

    // ✅ WAJIB: selalu balikin chat meski pesan dobel
    $thread->update([
        'deleted_low_at'  => null,
        'deleted_high_at' => null,
    ]);

    $last = ChatMessage::where('thread_id', $thread->id)
        ->where('sender_pelapor_id', $uid)
        ->latest('id')
        ->first();

    $alreadySent =
        $last &&
        $last->body === $autoText &&
        $last->created_at &&
        $last->created_at->gt(now()->subSeconds(15));

    if (!$alreadySent) {
        ChatMessage::create([
            'thread_id'         => $thread->id,
            'sender_pelapor_id' => $uid,
            'message_type'      => 'text',
            'body'              => $autoText,
        ]);

        $thread->update(['last_message_at' => now()]);
    }

    return redirect()->route('chat.show', $thread->id);
}


    public function destroy(ChatThread $thread)
{
    $uid = auth()->id();
    if (!in_array($uid, [$thread->user_low_id, $thread->user_high_id])) abort(403);

    if ($uid == $thread->user_low_id) {
        $thread->deleted_low_at = now();
    } else {
        $thread->deleted_high_at = now();
    }
    $thread->save();

    // ✅ kalau dua-duanya sudah delete -> hapus permanen thread & semua pesan
    if ($thread->deleted_low_at && $thread->deleted_high_at) {
        ChatMessage::where('thread_id', $thread->id)->delete();
        $thread->delete();
    }

    return redirect()->route('chat.index')->with('success', 'Chat berhasil dihapus.');
}

public function sendPickupForm(ChatThread $thread)
{
    $uid = auth()->id();
    if (!in_array($uid, [$thread->user_low_id, $thread->user_high_id])) abort(403);

    // ✅ hanya penemu yang boleh kirim form
    if (!$this->isFinderUser($thread, $uid)) abort(403);

    // anti spam / anti dobel kepencet
    $template = $this->pickupFormTemplate($thread);

    $last = ChatMessage::where('thread_id', $thread->id)->latest('id')->first();
    if ($last && $last->message_type === 'system' && $last->body === $template && $last->created_at?->gt(now()->subSeconds(10))) {
        return back();
    }

    ChatMessage::create([
        'thread_id' => $thread->id,
        'sender_pelapor_id' => $uid,
        'message_type' => 'system', // aman karena enum kamu sudah ada 'system'
        'body' => $template,
    ]);

    $thread->update(['last_message_at' => now()]);

    return back()->with('success', 'Form pengambilan barang terkirim.');
}

private function pickupFormTemplate(ChatThread $thread): string
{
    // Kamu bisa tambah info barang/claim di sini kalau mau.
    // Yang penting: formatnya enak dicopy.
    return <<<TXT
[FORM PENGAMBILAN BARANG]
Silakan COPY, isi, lalu kirim balik di chat ini.

1) Nama lengkap:
2) NIM/NPM:
3) Prodi/Fakultas:
4) No. HP:
5) Email:
6) Bukti kepemilikan (contoh: foto barang lama/nota/serial number/ciri spesifik):
7) Ciri-ciri barang (detail):
8) Waktu & lokasi pengambilan yang diinginkan:
9) Catatan tambahan:

Terima kasih 🙏
TXT;
}

private function isFinderUser(ChatThread $thread, int $uid): bool
{
    if ($thread->barang_type === 'hilang') {
        $lost = BarangHilang::where('barang_id', $thread->barang_id)->first();
        if (!$lost) return false;

        // penemu = bukan pelapor barang hilang
        return (int) $lost->pelapor_id !== $uid;
    }

    if ($thread->barang_type === 'temuan') {
        $found = Temuan::where('penemuan_id', $thread->barang_id)->first();
        if (!$found) return false;

        // penemu = pelapor temuan
        return (int) $found->pelapor_id === $uid;
    }

    return false;
}

}
