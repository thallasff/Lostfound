<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';

    protected $fillable = [
        'thread_id',
        'barang_type',   // 'hilang' | 'temuan'
        'barang_id',
        'requester_id',  // yang ngaku (owner/pengambil)
        'holder_id',     // yang pegang barang (penemu)
        'status',        // requested|form_sent|submitted|approved|rejected|handover_uploaded|closed
        'form_data',     // JSON/TEXT (detail dari requester)
        'foto_klaim_1',
        'foto_klaim_2',
        'foto_klaim_3',
        'foto_serah_terima',
        'approved_at',
        'rejected_at',
        'closed_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ChatThread::class, 'thread_id');
    }
}
