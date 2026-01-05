<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'thread_id',
        'sender_pelapor_id',
        'message_type',
        'body',
        'payload',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ChatThread::class, 'thread_id', 'id');
    }
}
