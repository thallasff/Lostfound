<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangHilang;

class LostController extends Controller
{
    public function create()
    {
        return view('lapor.hilang'); // sesuaikan path view kamu
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'        => 'required|string|max:255',
            'kategori'           => 'required|string|max:50',
            'deskripsi_singkat'  => 'nullable|string',

            // foto optional, max 3
            'foto_barang'        => 'nullable|array|max:3',
            'foto_barang.*'      => 'image|mimes:jpg,jpeg,png|max:2048',

            'warna'              => 'nullable|string|max:50',
            'merek'              => 'nullable|string|max:80',
            'kondisi_terakhir'   => 'nullable|in:baik,rusak',

            'latitude'           => 'required|numeric|between:-90,90',
            'longitude'          => 'required|numeric|between:-180,180',
            'lokasi_gedung' => 'required|string|max:120',

            // DIPISAH
            'tanggal_hilang'     => 'required|date',
            'waktu_hilang'       => 'required|date_format:H:i',

            'catatan_tambahan'   => 'nullable|string',
        ]);

        $u = Auth::user();
        $username = $u->username ?? $u->name ?? '-';

        // simpan foto 1-3 (opsional)
        $paths = [null, null, null];
        if ($request->hasFile('foto_barang')) {
            $files = $request->file('foto_barang');
            $files = array_slice($files, 0, 3);

            foreach ($files as $i => $file) {
                $paths[$i] = $file->store('foto_barang_hilang', 'public');
            }
        }

        BarangHilang::create([
            'pelapor_id'        => Auth::id(),
            'username_pelapor'  => $username,

            'nama_barang'       => $request->nama_barang,
            'kategori'          => $request->kategori,
            'deskripsi_singkat' => $request->deskripsi_singkat,

            'foto_barang_1'     => $paths[0],
            'foto_barang_2'     => $paths[1],
            'foto_barang_3'     => $paths[2],

            'warna'             => $request->warna,
            'merek'             => $request->merek,
            'kondisi_terakhir'  => $request->kondisi_terakhir,

            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'lokasi_gedung' => $request->lokasi_gedung,

            // kolom baru (dipisah)
            'tanggal_hilang'    => $request->tanggal_hilang,
            'waktu_hilang'      => $request->waktu_hilang,

            'catatan_tambahan'  => $request->catatan_tambahan,
            'status'            => 'belum ditemukan',
        ]);

        return redirect()
            ->route('lost.create')
            ->with('success', 'Laporan barang hilang berhasil dikirim!');
    }
}
