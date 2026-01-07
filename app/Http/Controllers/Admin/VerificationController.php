<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangHilang;
use App\Models\Temuan;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    // =========================
    // LIST: Verifikasi Laporan (barang hilang)
    // =========================
    public function laporan(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $latest = DB::table('claims')
            ->selectRaw('MAX(id) as id, barang_id')
            ->where('barang_type', 'hilang')
            ->groupBy('barang_id');

        $rows = BarangHilang::query()
            ->leftJoinSub($latest, 'lc', function ($join) {
                $join->on('lc.barang_id', '=', 'barang_hilang.barang_id');
            })
            ->leftJoin('claims as c', 'c.id', '=', 'lc.id')
            ->select([
                'barang_hilang.*',
                DB::raw('c.id as claim_id'),
                DB::raw('c.status as claim_status'),
                DB::raw('c.handover_proof_photo as handover_proof_photo'),
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('barang_hilang.nama_barang', 'like', "%{$q}%")
                      ->orWhere('barang_hilang.username_pelapor', 'like', "%{$q}%")
                      ->orWhere('barang_hilang.kategori', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('barang_hilang.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.laporan.index', compact('rows', 'q'));
    }

    public function showLaporan(BarangHilang $item)
    {
        $claim = Claim::where('barang_type', 'hilang')
            ->where('barang_id', $item->barang_id)
            ->latest('id')
            ->first();

        $form = $claim?->form_payload;
        return view('admin.verifikasi.laporan.show', compact('item', 'claim', 'form'));
    }

    public function markSelesai(BarangHilang $item)
    {
        $item->update(['status' => 'selesai']);

        $claim = Claim::where('barang_type', 'hilang')
            ->where('barang_id', $item->barang_id)
            ->latest('id')
            ->first();

        if ($claim) {
            $claim->update([
                'status' => 'closed_by_admin',
                'decided_at' => now(),
            ]);
        }

        return back()->with('success', 'Laporan ditandai selesai. Marker akan hilang dari map.');
    }

    public function rejectHandover(Request $request, BarangHilang $item)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $claim = Claim::where('barang_type', 'hilang')
            ->where('barang_id', $item->barang_id)
            ->latest('id')
            ->first();

        if ($claim) {
            $claim->update([
                'status' => 'rejected_by_admin',
                'decided_at' => now(),
            ]);
        }

        return back()->with('success', 'Bukti ditolak. Minta penemu upload ulang bukti serah-terima.');
    }


    // =========================
    // LIST: Verifikasi Penemuan
    // =========================
    public function penemuan(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $latest = DB::table('claims')
            ->selectRaw('MAX(id) as id, barang_id')
            ->where('barang_type', 'temuan')
            ->groupBy('barang_id');

        $rows = Temuan::query()
            ->leftJoinSub($latest, 'lc', function ($join) {
                $join->on('lc.barang_id', '=', 'penemuan_barang.penemuan_id');
            })
            ->leftJoin('claims as c', 'c.id', '=', 'lc.id')
            ->select([
                'penemuan_barang.*',
                DB::raw('c.id as claim_id'),
                DB::raw('c.status as claim_status'),
                DB::raw('c.handover_proof_photo as handover_proof_photo'),
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('penemuan_barang.nama_barang', 'like', "%{$q}%")
                      ->orWhere('penemuan_barang.username_penemu', 'like', "%{$q}%")
                      ->orWhere('penemuan_barang.kategori', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('penemuan_barang.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.penemuan.index', compact('rows', 'q'));
    }

    // DETAIL: Penemuan + claim + bukti serah terima
    public function showPenemuan(Temuan $item)
    {
        $claim = Claim::where('barang_type', 'temuan')
            ->where('barang_id', $item->penemuan_id)
            ->latest('id')
            ->first();

        $form = $claim?->form_payload;

        return view('admin.verifikasi.penemuan.show', compact('item', 'claim', 'form'));
    }

    public function markSelesaiPenemuan(Temuan $item)
    {
        $item->update(['status_verifikasi' => 'selesai']);

        $claim = Claim::where('barang_type', 'temuan')
            ->where('barang_id', $item->penemuan_id)
            ->latest('id')
            ->first();

        if ($claim) {
            $claim->update([
                'status' => 'closed_by_admin',
                'decided_at' => now(),
            ]);
        }

        return back()->with('success', 'Penemuan ditandai selesai. Marker akan hilang dari map.');
    }

    public function rejectHandoverPenemuan(Request $request, Temuan $item)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $claim = Claim::where('barang_type', 'temuan')
            ->where('barang_id', $item->penemuan_id)
            ->latest('id')
            ->first();

        if ($claim) {
            $claim->update([
                'status' => 'rejected_by_admin',
                'decided_at' => now(),
            ]);
        }

        return back()->with('success', 'Bukti ditolak. Minta penemu upload ulang bukti serah-terima.');
    }
}
