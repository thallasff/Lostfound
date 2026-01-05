<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatThread extends Model
{
    protected $table = 'chat_threads';

    protected $fillable = [
        'barang_type',
        'barang_id',
        'user_low_id',
        'user_high_id',
        'last_message_at',
        'deleted_low_at',
        'deleted_high_at',
    ];

    protected $casts = [
        'last_message_at'  => 'datetime',
        'deleted_low_at'   => 'datetime',
        'deleted_high_at'  => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'thread_id', 'id')->orderBy('created_at');
    }
}
