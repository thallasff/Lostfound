<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Models\UserPelapor;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    private int $supportId = 0;

    public function index()
    {
        $threads = ChatThread::query()
            ->where('barang_type', 'support')
            ->where('barang_id', 0)
            ->orderByDesc('last_message_at')
            ->get();

        // ambil pelapor_id dari tiap thread (yang bukan 0)
        $pelaporIds = $threads->map(function ($t) {
            return ((int)$t->user_low_id === $this->supportId) ? (int)$t->user_high_id : (int)$t->user_low_id;
        })->unique()->values();

        $users = UserPelapor::with('profile')
            ->whereIn('pelapor_id', $pelaporIds)
            ->get()
            ->keyBy('pelapor_id');

        // hitung unread dari pelapor (pesan yg belum dibaca admin)
        $unread = ChatMessage::query()
            ->selectRaw('thread_id, COUNT(*) as total')
            ->whereIn('thread_id', $threads->pluck('id'))
            ->whereNull('read_at')
            ->where('sender_pelapor_id', '!=', $this->supportId) // pesan dari pelapor
            ->groupBy('thread_id')
            ->pluck('total', 'thread_id');

        return view('admin.moderate-chat', compact('threads', 'users', 'unread'));
    }

    public function show(ChatThread $thread)
    {
        // pastikan ini thread support
        $isSupport =
            $thread->barang_type === 'support' &&
            (int)$thread->barang_id === 0 &&
            ((int)$thread->user_low_id === $this->supportId || (int)$thread->user_high_id === $this->supportId);

        if (!$isSupport) abort(404);

        // tandain pesan pelapor sebagai read (admin buka)
        ChatMessage::where('thread_id', $thread->id)
            ->whereNull('read_at')
            ->where('sender_pelapor_id', '!=', $this->supportId)
            ->update(['read_at' => now()]);

        $threads = ChatThread::query()
            ->where('barang_type', 'support')
            ->where('barang_id', 0)
            ->orderByDesc('last_message_at')
            ->get();

        $pelaporId = ((int)$thread->user_low_id === $this->supportId) ? (int)$thread->user_high_id : (int)$thread->user_low_id;

        $users = UserPelapor::with('profile')
            ->whereIn('pelapor_id', $threads->map(fn($t) => ((int)$t->user_low_id === $this->supportId) ? (int)$t->user_high_id : (int)$t->user_low_id)->unique())
            ->get()
            ->keyBy('pelapor_id');

        $otherUser = $users[$pelaporId] ?? UserPelapor::with('profile')->where('pelapor_id', $pelaporId)->first();

        $messages = ChatMessage::where('thread_id', $thread->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // unread map buat list kiri
        $unread = ChatMessage::query()
            ->selectRaw('thread_id, COUNT(*) as total')
            ->whereIn('thread_id', $threads->pluck('id'))
            ->whereNull('read_at')
            ->where('sender_pelapor_id', '!=', $this->supportId)
            ->groupBy('thread_id')
            ->pluck('total', 'thread_id');

        return view('admin.moderate-chat', compact('threads', 'thread', 'messages', 'users', 'otherUser', 'pelaporId', 'unread'));
    }

    public function send(Request $request, ChatThread $thread)
    {
        // pastikan support thread
        $isSupport =
            $thread->barang_type === 'support' &&
            (int)$thread->barang_id === 0 &&
            ((int)$thread->user_low_id === $this->supportId || (int)$thread->user_high_id === $this->supportId);

        if (!$isSupport) abort(404);

        $data = $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_pelapor_id' => $this->supportId, // admin = 0
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
}
