<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Dashboard minimal (nanti bisa kamu isi statistik)
            return view('admin.dashboard', [
        // sementara dummy, nanti kita ganti query beneran
        'jumlahLaporan' => 42,
        'laporanDiverifikasi' => 30,
        'penggunaAktif' => 25,
        'chatBelumDibalas' => 5,
    ]);
    }

    // Kalau route kamu ada moderasi chat
    public function moderateChat()
    {
        return view('admin.moderate-chat');
    }
}
