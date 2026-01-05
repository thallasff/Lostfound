<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    // Nama tabel (karena tidak mengikuti default laravel)
    protected $table = 'laporan';

    // Primary key
    protected $primaryKey = 'laporan_id';

    // Boleh diisi
    protected $fillable = [
        'pelapor_id',
        'admin_id',
        'barang_id',
        'tanggal_laporan',
        'status_verifikasi',
        'deskripsi',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Laporan dibuat oleh 1 user pelapor
    public function pelapor()
    {
        return $this->belongsTo(UserPelapor::class, 'pelapor_id', 'pelapor_id');
    }

    // Laporan diverifikasi oleh admin (boleh null)
    public function admin()
    {
        return $this->belongsTo(UserAdmin::class, 'admin_id', 'admin_id');
    }

    // Setiap laporan punya data barang hilang
    public function barang()
    {
        return $this->belongsTo(BarangHilang::class, 'barang_id', 'barang_id');
    }
}
