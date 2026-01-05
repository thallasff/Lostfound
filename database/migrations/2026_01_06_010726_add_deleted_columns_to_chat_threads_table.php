<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('chat_threads', function (Blueprint $table) {
        $table->timestamp('deleted_low_at')->nullable()->after('last_message_at');
        $table->timestamp('deleted_high_at')->nullable()->after('deleted_low_at');
    });
}

public function down(): void
{
    Schema::table('chat_threads', function (Blueprint $table) {
        $table->dropColumn(['deleted_low_at', 'deleted_high_at']);
    });
}
};
