<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangHilang;
use App\Models\Temuan;

class HomeController extends Controller
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
            ->get();

        return view('web.home', compact('lost','found'));
    }
}
