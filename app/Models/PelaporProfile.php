<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaporProfile extends Model
{
    protected $table = 'pelapor_profiles';

    protected $fillable = [
        'pelapor_id',
        'nama_lengkap',
        'status',
        'fakultas',
        'jurusan',
        'no_ponsel',
        'foto_profil',
    ];

    public function pelapor()
    {
        return $this->belongsTo(UserPelapor::class, 'pelapor_id', 'pelapor_id');
    }
}
