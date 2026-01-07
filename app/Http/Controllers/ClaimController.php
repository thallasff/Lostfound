<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\BarangHilang;
use App\Models\Temuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    // PENEMU (owner) kirim template form pengambilan ke chat
    public function sendForm(Request $request, ChatThread $thread)
    {
        $userId = auth()->id();

        // pastikan user adalah participant thread
        if (!in_array($userId, [$thread->user_low_id, $thread->user_high_id])) {
            abort(403);
        }

        // kalau sudah ada claim aktif di thread ini, jangan bikin lagi
        $existing = Claim::where('thread_id', $thread->id)
            ->whereNotIn('status', ['closed', 'closed_by_admin'])
            ->latest('id')
            ->first();

        if ($existing) {
            return back()->with('info', 'Form pengambilan sudah pernah dibuat di chat ini.');
        }

        // owner = yang klik tombol ini (penemu)
        $ownerId = $userId;
        $requesterId = ($thread->user_low_id == $userId) ? $thread->user_high_id : $thread->user_low_id;

        DB::transaction(function () use ($thread, $ownerId, $requesterId) {
            $claim = Claim::create([
                'thread_id'    => $thread->id,
                'barang_type'  => $thread->barang_type, // 'hilang' | 'temuan'
                'barang_id'    => $thread->barang_id,
                'requester_id' => $requesterId,
                'owner_id'     => $ownerId,
                'status'       => 'form_sent',
                'form_payload' => null,
                'decided_at'   => null,
            ]);

            $pesan = "📌 *Form Pengambilan Barang*\n"
                . "Tolong isi detail berikut supaya saya bisa verifikasi:\n"
                . "1) Ciri spesifik barang (warna/brand/isi)\n"
                . "2) Bukti kepemilikan (nota/foto lama/serial number)\n"
                . "3) Perkiraan waktu & lokasi terakhir\n"
                . "4) Kontak & waktu janjian ambil\n\n"
                . "Nanti kamu isi lewat form (atau balas sesuai format).\n"
                . "ID Klaim: #{$claim->id}";

ChatMessage::create([
    'thread_id'         => $thread->id,
    'sender_pelapor_id' => $ownerId,
    'message_type'      => 'system', // atau 'text' kalau mau
    'body'              => $pesan,
]);

            $thread->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Template form pengambilan terkirim.');
    }

    // REQUESTER submit jawaban form + upload 1 foto bukti kepemilikan
    public function submit(Request $request, Claim $claim)
{
    $userId = auth()->id();

    if ($userId !== (int) $claim->requester_id) abort(403);

    if (!in_array($claim->status, ['form_sent', 'requested'])) {
        return back()->with('info', 'Status klaim tidak bisa disubmit lagi.');
    }

    $data = $request->validate([
        'ciri_barang' => 'required|string|max:1000',
        'bukti_kepemilikan' => 'nullable|string|max:1000', // boleh tetap ada teksnya kalau mau
        'kronologi' => 'nullable|string|max:1500',
        'kontak' => 'required|string|max:200',
        // ✅ foto_bukti dihapus
    ]);

    DB::transaction(function () use ($claim, $data) {
        $claim->update([
            'form_payload' => [
                'ciri_barang' => $data['ciri_barang'],
                'bukti_kepemilikan' => $data['bukti_kepemilikan'] ?? null,
                'kronologi' => $data['kronologi'] ?? null,
                'kontak' => $data['kontak'],
            ],
            'status' => 'submitted',
            'decided_at' => null,
        ]);

        ChatMessage::create([
    'thread_id'         => $claim->thread_id,
    'sender_pelapor_id' => $claim->requester_id,
    'message_type'      => 'text',
    'body'              => "✅ Form pengambilan sudah diisi untuk Klaim #{$claim->id}. Silakan cek dan tentukan *Setuju/Tolak*.",
]);


        ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
    });

    return back()->with('success', 'Form pengambilan terkirim ke penemu.');
}


    // OWNER approve / reject
    public function decide(Request $request, Claim $claim)
    {
        $userId = auth()->id();
        if ($userId !== (int) $claim->owner_id) abort(403);

        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500',
        ]);

        if ($claim->status !== 'submitted') {
            return back()->with('info', 'Klaim belum disubmit / status tidak sesuai.');
        }

        DB::transaction(function () use ($claim, $data) {
            if ($data['decision'] === 'approve') {
                $claim->update([
                    'status' => 'approved',
                    'decided_at' => now(),
                ]);

               ChatMessage::create([
    'thread_id'         => $claim->thread_id,
    'sender_pelapor_id' => $claim->owner_id,
    'message_type'      => 'text',
    'body'              => "✅ Klaim #{$claim->id} *DISETUJUI*. Silakan janjian ambil barang.\nSetelah serah-terima, saya akan upload bukti penyerahan untuk diverifikasi admin.",
]);

            } else {
                $claim->update([
                    'status' => 'rejected',
                    'decided_at' => now(),
                ]);

                $note = $data['note'] ? ("\nCatatan: ".$data['note']) : '';

ChatMessage::create([
    'thread_id'         => $claim->thread_id,
    'sender_pelapor_id' => $claim->owner_id,
    'message_type'      => 'text',
    'body'              => "❌ Klaim #{$claim->id} *DITOLAK*.".$note,
]);

            }

            ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Keputusan klaim tersimpan.');
    }

    // OWNER upload bukti serah-terima (1 foto) -> status handover_uploaded -> nunggu admin
    public function uploadHandover(Request $request, Claim $claim)
    {
        $userId = auth()->id();
        if ((int)$uid !== (int)$claim->owner_id) abort(403);

        if (in_array($claim->status, ['closed','closed_by_admin'], true)) {
    return back()->with('info', 'Klaim ini sudah ditutup.');
}

        $data = $request->validate([
            'foto_serah_terima' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($claim, $request, $data) {
            $path = $request->file('foto_serah_terima')->store('claims/serah-terima', 'public');

            $claim->update([
                'handover_proof_photo' => $path,        // ✅ sesuai DB
                'status' => 'handover_uploaded',        // ✅ nunggu admin
                'decided_at' => null,
            ]);

            // 🔥 OPTIONAL: kalau mau langsung ilang dari map JANGAN di sini
            // Karena kamu bilang admin yang tentuin selesai/belum.
            // Jadi barang cukup ditandai "ditemukan" aja (bukan "selesai").

            if ($claim->barang_type === 'hilang') {
                BarangHilang::where('barang_id', $claim->barang_id)->update(['status' => 'ditemukan']);
            } else {
                Temuan::where('penemuan_id', $claim->barang_id)->update(['status_verifikasi' => 'ditemukan']);
            }

            $note = $data['catatan'] ? ("\nCatatan: ".$data['catatan']) : '';
            ChatMessage::create([
    'thread_id'         => $claim->thread_id,
    'sender_pelapor_id' => $claim->owner_id,
    'message_type'      => 'text',
    'body'              => "📷 Bukti serah-terima untuk Klaim #{$claim->id} sudah diupload. Menunggu verifikasi admin.",
]);


            ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Bukti serah-terima tersimpan. Menunggu verifikasi admin.');
    }

public function submitToAdmin(Request $request, Claim $claim)
{
    $uid = auth()->id();

    // ✅ hanya penemu/owner yang boleh submit ke admin
    if ((int)$uid !== (int)$claim->owner_id) abort(403);

    // ✅ hanya boleh kalau claim masih aktif
    if (in_array($claim->status, ['closed', 'closed_by_admin'], true)) {
        return back()->with('info', 'Klaim ini sudah ditutup.');
    }

    $data = $request->validate([
        'form_text' => 'required|string|min:10|max:5000',
        'foto_serah_terima' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $path = $request->file('foto_serah_terima')->store('claims/serah-terima', 'public');

    $claim->update([
        'form_payload' => ['text' => $data['form_text']],
        'handover_proof_photo' => $path,     // ✅ ini yang dipakai admin
        'status' => 'handover_uploaded',     // ✅ nunggu admin verifikasi
        'decided_at' => null,
    ]);

    // notifikasi ke chat (opsional)
    ChatMessage::create([
        'thread_id'         => $claim->thread_id,
        'sender_pelapor_id' => $uid,
        'message_type'      => 'system',
        'body'              => "📤 Bukti serah-terima sudah dikirim ke admin untuk verifikasi. (Klaim #{$claim->id})",
    ]);

    \App\Models\ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);

    return back()->with('success', 'Bukti berhasil dikirim ke admin. Tunggu verifikasi admin.');
}


}
