<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Temuan;

class FoundController extends Controller
{
    public function create()
    {
        return view('lapor.temu');
    }

public function store(Request $request)
{
    $request->validate([
        // Informasi Barang (wajib)
        'nama_barang' => 'required|string|max:255',
        'kategori' => 'required|string|max:50',
        'deskripsi_singkat' => 'nullable|string',

        // Foto barang 1-3 (minimal 1)
        'foto_barang' => 'required',
        'foto_barang.*' => 'image|mimes:jpg,jpeg,png|max:2048',

        // Detail tambahan (opsional)
        'warna' => 'nullable|string|max:50',
        'merek' => 'nullable|string|max:80',
        'kondisi_barang' => 'nullable|in:baik,rusak_ringan,rusak_berat',

        // Lokasi dari map (wajib)
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'lokasi_gedung' => 'required|string|max:120',

        // Waktu ditemukan (opsional, default now)
        'waktu_ditemukan' => 'nullable|date',
    ]);

    // batasi max 3 foto (paksa backend)
    $files = $request->file('foto_barang');
    if (is_array($files) && count($files) > 3) {
        return back()->withErrors(['foto_barang' => 'Maksimal 3 foto.'])->withInput();
    }

    $paths = [null, null, null];
    if (is_array($files)) {
        foreach ($files as $i => $file) {
            if ($i >= 3) break;
            $paths[$i] = $file->store('foto_barang', 'public');
        }
    } else {
        // kalau browser ngirim single file (jarang), tetap aman
        $paths[0] = $request->file('foto_barang')->store('foto_barang', 'public');
    }

    $username = Auth::user()->username ?? Auth::user()->name ?? 'user';

    Temuan::create([
        'pelapor_id' => Auth::id(),
        'username_penemu' => $username,

        'nama_barang' => $request->nama_barang,
        'kategori' => $request->kategori,
        'deskripsi_singkat' => $request->deskripsi_singkat,

        'foto_barang_1' => $paths[0], // wajib
        'foto_barang_2' => $paths[1],
        'foto_barang_3' => $paths[2],

        'warna' => $request->warna,
        'merek' => $request->merek,
        'kondisi_barang' => $request->kondisi_barang,

        'latitude' => $request->latitude,
        'longitude' => $request->longitude,

        'waktu_ditemukan' => $request->waktu_ditemukan ?? now(),
        'status_verifikasi' => 'belum diverifikasi',
        'lokasi_gedung' => $request->lokasi_gedung,
    ]);

    return redirect()->route('found.create')->with('success', 'Laporan penemuan berhasil dikirim!');
    try {
   // create...
} catch (\Throwable $e) {
   \Log::error($e->getMessage());
   return back()->withInput()->with('error', 'Gagal menyimpan laporan. Coba lagi ya.');
}

}
}
