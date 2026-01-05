<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_threads', function (Blueprint $table) {
            $table->id();

            // optional: chat terkait barang (bisa barang_hilang / penemuan_barang)
            $table->string('barang_type')->nullable(); // contoh: 'barang_hilang' atau 'penemuan_barang'
            $table->unsignedBigInteger('barang_id')->nullable();

            // dua user pelapor yang chatting (pakai low/high biar unik)
            $table->unsignedBigInteger('user_low_id');
            $table->unsignedBigInteger('user_high_id');

            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            $table->unique(['barang_type', 'barang_id', 'user_low_id', 'user_high_id'], 'thread_unique_pair');
            $table->index(['user_low_id']);
            $table->index(['user_high_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_threads');
    }
};
