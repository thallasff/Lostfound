<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengambilanBarang extends Model
{
    protected $table = 'pengambilan_barang';

    protected $fillable = [
        'thread_id',
        'item_type',
        'item_id',
        'pemilik_id',
        'penemu_id',
        'status',
        'jawaban_pemilik',
        'bukti_pemilik_1',
        'bukti_pemilik_2',
        'bukti_penyerahan_1',
        'bukti_penyerahan_2',
        'bukti_penyerahan_3',
    ];
}
