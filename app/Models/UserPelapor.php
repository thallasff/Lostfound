<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserPelapor extends Authenticatable
{
    use Notifiable;

    protected $table = 'pelapor';
    protected $primaryKey = 'pelapor_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    public function profile()
    {
        return $this->hasOne(PelaporProfile::class, 'pelapor_id', 'pelapor_id');
    }
}
