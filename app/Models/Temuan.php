<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temuan extends Model
{
    protected $table = 'penemuan_barang';
    protected $primaryKey = 'penemuan_id';

    protected $fillable = [
        'pelapor_id',
        'username_penemu',

        'nama_barang',
        'kategori',
        'deskripsi_singkat',

        'foto_barang_1',
        'foto_barang_2',
        'foto_barang_3',

        'warna',
        'merek',
        'kondisi_barang',

        'latitude',
        'longitude',

        'waktu_ditemukan',
        'status_verifikasi',
        'lokasi_gedung',
    ];

    protected $casts = [
        'waktu_ditemukan' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // app/Models/Temuan.php
public function getRouteKeyName()
{
    return 'penemuan_id';
}

}
