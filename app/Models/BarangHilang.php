<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangHilang extends Model
{
    protected $table = 'barang_hilang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'pelapor_id',
        'username_pelapor',
        'nama_barang',
        'kategori',
        'deskripsi_singkat',
        'foto_barang_1',
        'foto_barang_2',
        'foto_barang_3',
        'warna',
        'merek',
        'kondisi_terakhir',
        'latitude',
        'longitude',
        'lokasi_gedung',
        'tanggal_hilang',
        'waktu_hilang',
        'catatan_tambahan',
        'status',
    ];

    protected $casts = [
        'terakhir_terlihat_at' => 'datetime',
        'rentang_mulai' => 'datetime',
        'rentang_selesai' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Relasi ke tabel pelapor
    // GANTI model ini sesuai user kamu (contoh: Pelapor / User / PelaporModel)
    public function pelapor()
    {
        return $this->belongsTo(\App\Models\UserPelapor::class, 'pelapor_id', 'pelapor_id');
        // kalau PK pelapor kamu "id", ubah jadi:
        // return $this->belongsTo(\App\Models\User::class, 'pelapor_id', 'id');
    }

    // NOTE:
    // Kolom admin_id udah tidak ada di tabel barang_hilang versi baru,
    // jadi relasi admin dihapus dulu.
}
