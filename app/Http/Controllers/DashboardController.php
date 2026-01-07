<?php

namespace App\Http\Controllers;

use App\Models\BarangHilang;
use App\Models\Temuan;

class DashboardController extends Controller
{
    public function index()
    {
        $lost = BarangHilang::select(
                'barang_id','nama_barang','deskripsi_singkat',
                'kategori','lokasi_gedung',
                'latitude','longitude',
                'foto_barang_1',
                'tanggal_hilang','waktu_hilang',
                'status'
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', 'selesai')  
            ->get();

        $found = Temuan::select(
                'penemuan_id','nama_barang','deskripsi_singkat',
                'kategori','lokasi_gedung',
                'latitude','longitude',
                'foto_barang_1',
                'waktu_ditemukan',
                'status_verifikasi'
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotIn('status_verifikasi', ['selesai','ditolak'])
            ->get();

        return view('web.dashboard', compact('lost','found'));
    }
}
