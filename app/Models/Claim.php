<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';

    protected $fillable = [
        'thread_id',
        'barang_type',           // 'hilang' | 'temuan'
        'barang_id',
        'requester_id',          // yang ngaku/pemilik
        'owner_id',              // yang pegang barang (penemu/holder)
        'status',                // requested, form_sent, submitted, approved, rejected, handover_uploaded, closed, dll
        'form_payload',          // JSON
        //'claimant_proof_photo',  // bukti kepemilikan (foto)
        'handover_proof_photo',  // bukti serah terima (foto)
        'decided_at',
    ];

    protected $casts = [
        'form_payload' => 'array',
        'decided_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ChatThread::class, 'thread_id');
    }
}
