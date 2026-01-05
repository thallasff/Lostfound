<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Chat;
use App\Models\ChatThread;
use App\Models\BarangHilang;
use App\Models\Temuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    // Penemu (holder) kirim template form pengambilan ke chat
    public function sendForm(Request $request, ChatThread $thread)
    {
        $userId = auth()->id();

        // pastikan user adalah participant thread
        if (!in_array($userId, [$thread->user_low_id, $thread->user_high_id])) {
            abort(403);
        }

        // kalau sudah ada claim aktif di thread ini, jangan bikin lagi
        $existing = Claim::where('thread_id', $thread->id)
            ->whereNotIn('status', ['closed'])
            ->latest('id')
            ->first();

        if ($existing) {
            return back()->with('info', 'Form pengambilan sudah pernah dibuat di chat ini.');
        }

        // holder = yang klik tombol ini (penemu)
        $holderId = $userId;
        $requesterId = ($thread->user_low_id == $userId) ? $thread->user_high_id : $thread->user_low_id;

        DB::transaction(function () use ($thread, $holderId, $requesterId) {
            $claim = Claim::create([
                'thread_id'    => $thread->id,
                'barang_type'  => $thread->barang_type,
                'barang_id'    => $thread->barang_id,
                'requester_id' => $requesterId,
                'holder_id'    => $holderId,
                'status'       => 'form_sent',
                'form_data'    => null,
            ]);

            // kirim pesan template ke chat (DM style)
            $pesan = "📌 *Form Pengambilan Barang*\n"
                . "Tolong isi detail berikut supaya saya bisa verifikasi:\n"
                . "1) Ciri spesifik barang (warna/brand/isi)\n"
                . "2) Bukti kepemilikan (nota/foto lama/serial number)\n"
                . "3) Perkiraan waktu & lokasi terakhir\n"
                . "4) Kontak & waktu janjian ambil\n\n"
                . "Klik tombol 'Isi Form Pengambilan' (nanti di UI) / atau balas dengan format di atas.\n"
                . "ID Klaim: #{$claim->id}";

            Chat::create([
                'thread_id' => $thread->id,
                'sender_pelapor_id' => $holderId,
                'receiver_pelapor_id' => $requesterId,
                'pesan' => $pesan,
                'waktu_kirim' => now(),
            ]);

            $thread->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Template form pengambilan terkirim.');
    }

    // Requester submit jawaban form + upload foto bukti
    public function submit(Request $request, Claim $claim)
    {
        $userId = auth()->id();

        if ($userId !== (int)$claim->requester_id) abort(403);
        if (!in_array($claim->status, ['form_sent', 'requested'])) {
            return back()->with('info', 'Status klaim tidak bisa disubmit lagi.');
        }

        $data = $request->validate([
            'ciri_barang' => 'required|string|max:1000',
            'bukti_kepemilikan' => 'nullable|string|max:1000',
            'kronologi' => 'nullable|string|max:1500',
            'kontak' => 'required|string|max:200',
            'foto_klaim' => 'nullable|array|max:3',
            'foto_klaim.*' => 'image|max:4096',
        ]);

        DB::transaction(function () use ($claim, $data, $request) {
            $paths = [null, null, null];

            if ($request->hasFile('foto_klaim')) {
                foreach ($request->file('foto_klaim') as $i => $file) {
                    if ($i > 2) break;
                    $paths[$i] = $file->store('claims/klaim', 'public');
                }
            }

            $claim->update([
                'form_data' => [
                    'ciri_barang' => $data['ciri_barang'],
                    'bukti_kepemilikan' => $data['bukti_kepemilikan'] ?? null,
                    'kronologi' => $data['kronologi'] ?? null,
                    'kontak' => $data['kontak'],
                ],
                'foto_klaim_1' => $paths[0],
                'foto_klaim_2' => $paths[1],
                'foto_klaim_3' => $paths[2],
                'status' => 'submitted',
            ]);

            Chat::create([
                'thread_id' => $claim->thread_id,
                'sender_pelapor_id' => $claim->requester_id,
                'receiver_pelapor_id' => $claim->holder_id,
                'pesan' => "✅ Form pengambilan sudah diisi untuk Klaim #{$claim->id}. Silakan cek dan tentukan *Setuju/Tolak*.",
                'waktu_kirim' => now(),
            ]);

            ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Form pengambilan terkirim ke penemu.');
    }

    // Holder approve / reject
    public function decide(Request $request, Claim $claim)
    {
        $userId = auth()->id();
        if ($userId !== (int)$claim->holder_id) abort(403);

        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500',
        ]);

        if (!in_array($claim->status, ['submitted'])) {
            return back()->with('info', 'Klaim belum disubmit / status tidak sesuai.');
        }

        DB::transaction(function () use ($claim, $data) {
            if ($data['decision'] === 'approve') {
                $claim->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);

                Chat::create([
                    'thread_id' => $claim->thread_id,
                    'sender_pelapor_id' => $claim->holder_id,
                    'receiver_pelapor_id' => $claim->requester_id,
                    'pesan' => "✅ Klaim #{$claim->id} *DISETUJUI*. Silakan janjian ambil barang.\n"
                             . "Setelah serah-terima, saya akan upload bukti penyerahan agar laporan selesai.",
                    'waktu_kirim' => now(),
                ]);
            } else {
                $claim->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                ]);

                $note = $data['note'] ? ("\nCatatan: ".$data['note']) : '';
                Chat::create([
                    'thread_id' => $claim->thread_id,
                    'sender_pelapor_id' => $claim->holder_id,
                    'receiver_pelapor_id' => $claim->requester_id,
                    'pesan' => "❌ Klaim #{$claim->id} *DITOLAK*.".$note,
                    'waktu_kirim' => now(),
                ]);
            }

            ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Keputusan klaim tersimpan.');
    }

    // Holder upload bukti serah-terima -> close claim -> barang hilang dari map
    public function uploadHandover(Request $request, Claim $claim)
    {
        $userId = auth()->id();
        if ($userId !== (int)$claim->holder_id) abort(403);

        if (!in_array($claim->status, ['approved'])) {
            return back()->with('info', 'Klaim belum disetujui / status tidak sesuai.');
        }

        $data = $request->validate([
            'foto_serah_terima' => 'required|image|max:4096',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($claim, $request, $data) {
            $path = $request->file('foto_serah_terima')->store('claims/serah-terima', 'public');

            $claim->update([
                'foto_serah_terima' => $path,
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            // 🔥 update status barang supaya hilang dari map
            if ($claim->barang_type === 'hilang') {
                BarangHilang::where('barang_id', $claim->barang_id)->update(['status' => 'selesai']);
            } else {
                // asumsi pk temuan = penemuan_id
                Temuan::where('penemuan_id', $claim->barang_id)->update(['status_verifikasi' => 'selesai']);
            }

            $note = $data['catatan'] ? ("\nCatatan: ".$data['catatan']) : '';
            Chat::create([
                'thread_id' => $claim->thread_id,
                'sender_pelapor_id' => $claim->holder_id,
                'receiver_pelapor_id' => $claim->requester_id,
                'pesan' => "📷 Bukti serah-terima untuk Klaim #{$claim->id} sudah diupload. Laporan ditutup.".$note,
                'waktu_kirim' => now(),
            ]);

            ChatThread::where('id', $claim->thread_id)->update(['last_message_at' => now()]);
        });

        return back()->with('success', 'Bukti serah-terima tersimpan. Barang akan hilang dari map.');
    }
}
