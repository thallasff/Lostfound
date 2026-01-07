<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE chat_threads MODIFY barang_type ENUM('hilang','temuan','support') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE chat_threads MODIFY barang_type ENUM('hilang','temuan') NULL");
    }
};
