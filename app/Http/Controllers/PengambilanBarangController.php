<?php

namespace App\Http\Controllers;

use App\Models\PengambilanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PengambilanBarangController extends Controller
{
    /**
     * PENEMU klik tombol "Kirim Form Pengambilan"
     * Dibuat dari halaman chat thread.
     */
    public function start(Request $request, $threadId)
    {
        $request->validate([
            'item_type' => 'required|in:temuan,hilang',
            'item_id'   => 'required|integer|min:1',
            'pemilik_id'=> 'required|integer|min:1',
            'penemu_id' => 'required|integer|min:1',
        ]);

        // only penemu yang boleh start
        if ((int) $request->penemu_id !== (int) Auth::id()) {
            abort(403, 'Hanya penemu yang boleh mengirim form pengambilan.');
        }

        if ((int) $request->pemilik_id === (int) $request->penemu_id) {
            abort(422, 'Pemilik dan penemu tidak boleh sama.');
        }

        // Cegah double request untuk item+thread yang sama (unik sudah di migration)
        $pickup = PengambilanBarang::firstOrCreate(
            [
                'thread_id' => (int) $threadId,
                'item_type' => $request->item_type,
                'item_id'   => (int) $request->item_id,
            ],
            [
                'pemilik_id' => (int) $request->pemilik_id,
                'penemu_id'  => (int) $request->penemu_id,
                'status'     => 'menunggu_pemilik',
            ]
        );

        // Auto kirim template chat dari penemu ke pemilik
        $msg = $this->buildTemplateMessage($pickup);
        $this->sendTemplateToChat(threadId: (int)$threadId, senderId: (int)Auth::id(), message: $msg);

        return redirect()->route('chat.show', ['thread' => $threadId])
            ->with('success', 'Form pengambilan sudah dikirim ke chat.');
    }

    /**
     * PEMILIK mengisi jawaban verifikasi + optional bukti (foto lama dsb)
     */
    public function pemilikSubmit(Request $request, PengambilanBarang $pickup)
    {
        if ((int) $pickup->pemilik_id !== (int) Auth::id()) {
            abort(403, 'Hanya pemilik yang boleh mengisi form pengambilan.');
        }

        if ($pickup->status !== 'menunggu_pemilik') {
            return back()->with('error', 'Form tidak bisa diisi pada status saat ini.');
        }

        $request->validate([
            'jawaban_pemilik' => 'required|string|min:10|max:3000',
            'bukti_pemilik'   => 'nullable|array|max:2',
            'bukti_pemilik.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $paths = [null, null];
        $files = $request->file('bukti_pemilik', []);
        $files = is_array($files) ? $files : [];

        foreach ($files as $i => $f) {
            if ($i >= 2) break;
            $paths[$i] = $f->store('bukti_pemilik', 'public');
        }

        $pickup->update([
            'jawaban_pemilik' => $request->jawaban_pemilik,
            'bukti_pemilik_1' => $paths[0],
            'bukti_pemilik_2' => $paths[1],
            'status'          => 'menunggu_konfirmasi_penemu',
        ]);

        // notif ke chat (optional)
        $this->sendTemplateToChat($pickup->thread_id, Auth::id(), "✅ Aku sudah isi form verifikasi. Mohon dicek ya.");

        return back()->with('success', 'Form verifikasi berhasil dikirim. Tunggu konfirmasi penemu.');
    }

    /**
     * PENEMU setuju klaim pemilik
     */
    public function penemuApprove(PengambilanBarang $pickup)
    {
        if ((int) $pickup->penemu_id !== (int) Auth::id()) {
            abort(403, 'Hanya penemu yang boleh konfirmasi.');
        }

        if ($pickup->status !== 'menunggu_konfirmasi_penemu') {
            return back()->with('error', 'Tidak bisa approve pada status saat ini.');
        }

        $pickup->update([
            'status' => 'menunggu_bukti_penyerahan',
        ]);

        $this->sendTemplateToChat($pickup->thread_id, Auth::id(),
            "✅ Oke, aku percaya ini barang kamu. Setelah kita serah-terima, aku bakal upload bukti penyerahan ya."
        );

        return back()->with('success', 'Klaim disetujui. Lanjut upload bukti penyerahan setelah serah-terima.');
    }

    /**
     * PENEMU menolak klaim
     */
    public function penemuReject(Request $request, PengambilanBarang $pickup)
    {
        if ((int) $pickup->penemu_id !== (int) Auth::id()) {
            abort(403, 'Hanya penemu yang boleh menolak.');
        }

        if ($pickup->status !== 'menunggu_konfirmasi_penemu') {
            return back()->with('error', 'Tidak bisa reject pada status saat ini.');
        }

        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        $pickup->update([
            'status' => 'ditolak',
        ]);

        $alasan = trim((string)($request->alasan ?? ''));
        $txt = "❌ Maaf, aku belum bisa menyetujui klaim ini.";
        if ($alasan !== '') $txt .= " Alasan: {$alasan}";

        $this->sendTemplateToChat($pickup->thread_id, Auth::id(), $txt);

        return back()->with('success', 'Klaim ditolak.');
    }

    /**
     * PENEMU upload bukti penyerahan (1-3 foto)
     * => status selesai
     */
    public function penemuBukti(Request $request, PengambilanBarang $pickup)
    {
        if ((int) $pickup->penemu_id !== (int) Auth::id()) {
            abort(403, 'Hanya penemu yang boleh upload bukti.');
        }

        if ($pickup->status !== 'menunggu_bukti_penyerahan') {
            return back()->with('error', 'Belum saatnya upload bukti penyerahan.');
        }

        $request->validate([
            'bukti_penyerahan'   => 'required|array|min:1|max:3',
            'bukti_penyerahan.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $paths = [null, null, null];
        $files = $request->file('bukti_penyerahan', []);

        foreach ($files as $i => $f) {
            if ($i >= 3) break;
            $paths[$i] = $f->store('bukti_penyerahan', 'public');
        }

        $pickup->update([
            'bukti_penyerahan_1' => $paths[0],
            'bukti_penyerahan_2' => $paths[1],
            'bukti_penyerahan_3' => $paths[2],
            'status'             => 'selesai',
        ]);

        $this->sendTemplateToChat($pickup->thread_id, Auth::id(),
            "✅ Barang sudah diserahkan. Bukti penyerahan sudah aku upload. Terima kasih!"
        );

        return back()->with('success', 'Bukti penyerahan tersimpan. Status pengambilan selesai.');
    }

    // =========================
    // Helpers
    // =========================

    private function buildTemplateMessage(PengambilanBarang $pickup): string
    {
        // Template “form” versi chat (pemilik tinggal copas/jawab)
        return
"🧾 *Form Pengambilan Barang* (harap diisi ya)

1) Ciri barang yang kamu cari (warna/merek/ciri khas):
2) Isi/kelengkapan (misal isi dompet / casing HP / strap):
3) Perkiraan lokasi terakhir kamu lihat:
4) Perkiraan tanggal & waktu hilang:
5) Bukti pendukung (foto lama/serupa) kalau ada.

Setelah kamu isi, aku akan review. Kalau cocok, kita atur serah-terima dan aku upload bukti penyerahan ✅";
    }

    /**
     * Auto-kirim message ke chat.
     * ✅ Default: insert ke tabel chat_messages
     * Kalau struktur chat kamu beda: ubah bagian TABLE/COLUMNS di sini.
     */
    private function sendTemplateToChat(int $threadId, int $senderId, string $message): void
    {
        // === UBAH INI kalau chat kamu beda ===
        $table = 'chat_messages';
        $colThread = 'thread_id';
        $colSender = 'sender_id';
        $colBody   = 'message';

        try {
            if (!Schema::hasTable($table)) {
                return; // fallback diam-diam
            }

            DB::table($table)->insert([
                $colThread => $threadId,
                $colSender => $senderId,
                $colBody   => $message,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // jangan bikin crash chat, cukup skip
            return;
        }
    }
}
