<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori'; // GANTI
    protected $primaryKey = 'kategori_id'; // GANTI

    protected $fillable = [
        'nama_kategori',
    ];

    public function barangHilang()
    {
        return $this->hasMany(BarangHilang::class, 'kategori_id');
    }

    public function temuan()
    {
        return $this->hasMany(Temuan::class, 'kategori_id');
    }
}
