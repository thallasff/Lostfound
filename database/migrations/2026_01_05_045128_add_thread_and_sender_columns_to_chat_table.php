<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat', function (Blueprint $table) {
            // tambahin kolom baru (biarin kolom lama pelapor_id/admin_id tetep ada biar gak ancur)
            $table->unsignedBigInteger('thread_id')->nullable()->after('chat_id');

            $table->unsignedBigInteger('sender_pelapor_id')->nullable()->after('thread_id');
            $table->unsignedBigInteger('receiver_pelapor_id')->nullable()->after('sender_pelapor_id');

            $table->timestamp('read_at')->nullable()->after('waktu_kirim');

            $table->index(['thread_id', 'created_at']);
            $table->index(['sender_pelapor_id']);
            $table->index(['receiver_pelapor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->dropIndex(['thread_id', 'created_at']);
            $table->dropIndex(['sender_pelapor_id']);
            $table->dropIndex(['receiver_pelapor_id']);

            $table->dropColumn(['thread_id', 'sender_pelapor_id', 'receiver_pelapor_id', 'read_at']);
        });
    }
};
